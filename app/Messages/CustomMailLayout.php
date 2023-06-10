<?php

namespace App\Messages;

use Illuminate\Notifications\Messages\MailMessage;

class CustomMailLayout extends MailMessage 
{
  /**
   * The "variant" of the notification (facebook, twitter, instagram, youtube, or etc).
   *
   * @var mixed
   */
  protected $socialMedia = [];

  /**
   * type email heading (it's not subject).
   *
   * @var string
   */
  public $headingMail = '';

  
  /**
   * The Markdown template to render (if applicable).
   *
   * @var string|null
   */
  public $markdown = 'mail.email-auth';

  /**
   * The view to render (if applicable).
   *
   * @var string|null
   */
  public function __construct()
  {
    $this->socialMedia = config('app.social_media.default');
  }

  
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

  public function headingMail($headingMail)
  {
    $this->headingMail = $headingMail;
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
      'headingMail' => $this->headingMail,
      'displayableActionUrl' => str_replace(['mailto:', 'tel:'], '', $this->actionUrl ?? ''),
    ];
  }
}