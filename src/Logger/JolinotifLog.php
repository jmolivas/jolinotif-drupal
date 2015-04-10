<?php

/**
 * @file
 * Contains \Drupal\jolinotif\Logger\JolinotifLog.
 */

namespace Drupal\jolinotif\Logger;

use Drupal\Core\Logger\LogMessageParserInterface;
use Drupal\Core\Logger\RfcLoggerTrait;
use Psr\Log\LoggerInterface;

use Joli\JoliNotif\Notification;
use Joli\JoliNotif\NotifierFactory;

/**
 * Logs events in the watchdog database table.
 */
class JolinotifLog implements LoggerInterface {
  use RfcLoggerTrait;

  /**
   * The message's placeholders parser.
   *
   * @var \Drupal\Core\Logger\LogMessageParserInterface
   */
  protected $parser;

  /**
   * Constructs a WhoopsLog object.
   * @param \Drupal\Core\Logger\LogMessageParserInterface $parser
   *   The parser to use when extracting message variables.
   */
  public function __construct(LogMessageParserInterface $parser) {
    $this->parser = $parser;
  }

  /**
   * {@inheritdoc}
   */
  public function log($level, $message, array $context = array()) {

    unset($context['backtrace']);

    $message_placeholders = $this->parser->parseMessagePlaceholders($message, $context);

    // Create a Notifier
    $notifier = NotifierFactory::create();
    $notification = new Notification();

    $body = str_replace(
      array_keys($message_placeholders),
      array_values($message_placeholders),
      $message
    );

    // Set attributes
    $notification->setTitle($context['channel']);
    $notification->setBody($body);

    // Send it
    $notifier->send($notification);
  }
}
