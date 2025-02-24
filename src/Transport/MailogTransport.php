<?php

namespace FilippoToso\LaravelMailog\Transport;

use Carbon\Carbon;
use FilippoToso\LaravelMailog\Enums\MessageAddressType;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;
use FilippoToso\LaravelMailog\Models\Message;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

class MailogTransport extends AbstractTransport
{
    /**
     * This method is called from the LogMessageSent listener
     *
     * @param SentMessage $message
     * @return void
     */
    public function listen(SentMessage $message): void
    {
        $this->doSend($message);
    }

    /**
     * Store the message in the database
     *
     * @param SentMessage $sentMessage
     * @return void
     */
    protected function doSend(SentMessage $sentMessage): void
    {
        $convertedEmail = MessageConverter::toEmail($sentMessage->getOriginalMessage());

        $subject = $convertedEmail->getSubject();

        foreach (Config::get('mailog.excluded') as $pattern) {
            if (preg_match($pattern, $subject)) {
                return;
            }
        }

        $messageId = $convertedEmail->generateMessageId();

        /** @disregard P1009 Undefined type */
        $message = Message::create($this->messageData($messageId, $convertedEmail));

        foreach ($convertedEmail->getAttachments() as $attachment) {
            $message->attachments()->create($this->attachmendData($messageId, $attachment));
        }

        $mapping = [
            'getFrom' => MessageAddressType::From,
            'getTo' => MessageAddressType::To,
            'getCc' => MessageAddressType::Cc,
            'getBcc' => MessageAddressType::Bcc,
            'getReturnPath' => MessageAddressType::ReturnPath,
            'getReplyTo' => MessageAddressType::ReplyTo,
        ];

        /** @var MessageAddressType $type */
        foreach ($mapping as $method => $type) {
            $addresses = call_user_func([$convertedEmail, $method]);

            // Handle Return Path exception
            if (is_null($addresses)) {
                continue;
            }

            foreach ($addresses as $address) {
                $message->addresses()->create($this->addressData($type, $address));
            }
        }
    }

    /**
     * Build the message data to be stored in the database
     * You can override this method to add more fields to the message
     *
     * @param $messageId
     * @param Email $converdedEmail
     * @return array
     */
    protected function messageData(string $messageId, Email $converdedEmail)
    {
        return [
            'date' => $converdedEmail->getDate() ?? Carbon::now(),
            'subject' => $converdedEmail->getSubject(),
            'text' => $converdedEmail->getTextBody(),
            'html' => $converdedEmail->getHtmlBody(),
            'path' => $this->storeEmail($messageId, $converdedEmail->toString()),
        ];
    }

    /**
     * Build the attachment data to be stored in the database
     * You can override this method to add more fields to the attachment
     *
     * @param string $messageId
     * @param DataPart $attachment
     * @return array
     */
    protected function attachmendData($messageId, DataPart $attachment)
    {
        return [
            'filename' => $attachment->getFilename(),
            'size' => strlen($attachment->getBody()),
            'path' => $this->storeAttachment($messageId, $attachment),
        ];
    }

    /**
     * Build the address data to be stored in the database
     * You can override this method to add more fields to the address
     *
     * @param MessageAddressType $type
     * @param Address $address
     * @return void
     */
    protected function addressData(MessageAddressType $type, Address $address)
    {
        return [
            'type' => $type,
            'address' => $address->getAddress(),
            'domain' => substr(strstr($address->getAddress(), '@'), 1),
            'name' => $address->getName(),
        ];
    }

    /**
     * Get the pre-configured storage
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function storage()
    {
        return Storage::disk(Config::get('mailog.filesystem.disk'));
    }

    /**
     * Store the original email content to the filesystem
     *
     * @param string $messageId
     * @param string $content
     * @return string
     */
    protected function storeEmail($messageId, $content)
    {
        $path = $this->basePath($messageId) . '/message.eml';

        $this->storage()->put($path, $content);

        return $path;
    }

    /**
     * Store an attachment
     *
     * @param string $messageId
     * @param DataPart $attachment
     * @return string
     */
    protected function storeAttachment($messageId, DataPart $attachment)
    {
        $extension = pathinfo($attachment->getFilename(), PATHINFO_EXTENSION);
        $path = $this->basePath($messageId) . '/attachments/' . Str::uuid() . '.' . $extension;

        $this->storage()->put($path, $attachment->getBody());

        return $path;
    }

    /**
     * Define the base path for the storing email content and attachments
     *
     * @param string $messageId
     * @return string
     */
    protected function basePath($messageId)
    {
        return Config::get('mailog.filesystem.path') . '/' . Carbon::now()->format('Y/m/d') . '/' . $messageId;
    }

    /**
     * Get the string representation of the transport.
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'mailog';
    }
}
