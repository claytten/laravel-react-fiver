<?php

namespace App\Messages;

use Illuminate\Notifications\Messages\MailMessage;

class CustomMailVerifyMessage extends MailMessage 
{
  //variable public for social media facebook, twitter, instagram and youtube
  /**
   * The "variant" of the notification (facebook, twitter, instagra, youtube, or etc).
   *
   * @var array
   */
  public $socialMedia = [];

  /**
   * The Markdown template to render (if applicable).
   *
   * @var string|null
   */
  public $markdown = 'mail.email-verify';

  
  public function facebook($url)
  {
    $this->socialMedia['facebook'] = $url;
    return $this;
  }

  public function twitter($url)
  {
    $this->socialMedia['twitter'] = $url;
    return $this;
  }

  public function instagram($url)
  {
    $this->socialMedia['instagram'] = $url;
    return $this;
  }

  public function youtube($url)
  {
    $this->socialMedia['youtube'] = $url;
    return $this;
  }

  /**
   * Get an array representation of the message.
   *
   * @return array
   */
  public function toArray()
  {
    return [
      'level' => $this->level,
      'subject' => $this->subject,
      'greeting' => $this->greeting,
      'salutation' => $this->salutation,
      'introLines' => $this->introLines,
      'outroLines' => $this->outroLines,
      'actionText' => $this->actionText,
      'actionUrl' => $this->actionUrl,
      'socialMedia' => $this->socialMedia,
      'displayableActionUrl' => str_replace(['mailto:', 'tel:'], '', $this->actionUrl ?? ''),
    ];
  }
}