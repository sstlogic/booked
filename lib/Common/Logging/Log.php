<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'vendor/autoload.php');
require_once(ROOT_DIR . 'lib/Config/namespace.php');

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;

class Log
{
    /**
     * @var Log
     */
    private static $_instance;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Logger
     */
    private $sqlLogger;

    private function __construct()
    {
        $config = Configuration::Instance();
        $logDir = $config->GetSectionKey(ConfigSection::LOGGING, ConfigKeys::LOGGING_DIR);
        if (empty($logDir)) {

            $this->logger = new NullLogger();
            $this->sqlLogger = new NullLogger();
            return;
        }

        $this->logger = new Logger('booked-application');
        $this->sqlLogger = new Logger('booked-sql');

        $logDir = rtrim($logDir, '/');
        $level = $this->GetLevelFromConfig($config->GetSectionKey(ConfigSection::LOGGING, ConfigKeys::LOGGING_LEVEL));

        $this->logger->pushHandler(new RotatingFileHandler($logDir . '/booked-application.log', 30, $level));
        $this->logger->pushProcessor(new IntrospectionProcessor($level, [], 1));
        $this->sqlLogger->pushHandler(new RotatingFileHandler($logDir . '/booked-sql.log', 30, $level));
    }

    private static function &GetInstance(): Log
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Log();
        }

        return self::$_instance;
    }

    /**
     * @param string $message
     * @param mixed|array $args
     */
    public static function Debug($message, $args = array())
    {
        if (!self::GetInstance()->logger->isHandling(Logger::DEBUG)) {
            return;
        }

        $args = self::enrichWithUser($args);

        try {
            self::GetInstance()->logger->debug($message, $args);
        } catch (Exception $ex) {
            die('Could not write to Booked log file. Please check your log configuration.');
        }
    }

    /**
     * @param string $message
     * @param mixed|array $args
     */
    public static function Info($message, $args = array())
    {
        if (!self::GetInstance()->logger->isHandling(Logger::INFO)) {
            return;
        }

        $args = self::enrichWithUser($args);

        try {
            self::GetInstance()->logger->info($message, $args);
        } catch (Exception $ex) {
            die('Could not write to Booked log file. Please check your log configuration.');
        }
    }

    /**
     * @param string $message
     * @param mixed $args
     */
    public static function Notice($message, $args = array())
    {
        if (!self::GetInstance()->logger->isHandling(Logger::NOTICE)) {
            return;
        }

        $args = self::enrichWithUser($args);

        try {
            self::GetInstance()->logger->notice($message, $args);
        } catch (Exception $ex) {
            die('Could not write to Booked log file. Please check your log configuration.');
        }
    }

    /**
     * @param string $message
     * @param mixed|array $args
     */
    public static function Warning($message, $args = array())
    {
        if (!self::GetInstance()->logger->isHandling(Logger::WARNING)) {
            return;
        }

        $args = self::enrichWithUser($args);

        try {
            self::GetInstance()->logger->warning($message, $args);
        } catch (Exception $ex) {
            die('Could not write to Booked log file. Please check your log configuration.');
        }
    }

    /**
     * @param string $message
     * @param mixed $args
     */
    public static function Error($message, $args = array())
    {
        if (!self::GetInstance()->logger->isHandling(Logger::ERROR)) {
            return;
        }

        $args = self::enrichWithUser($args);

        try {
            self::GetInstance()->logger->error($message, $args);
        } catch (Exception $ex) {
            die('Could not write to Booked log file. Please check your log configuration.');
        }
    }

    /**
     * @param string $message
     * @param mixed $args
     */
    public static function Critical($message, $args = array())
    {
        if (!self::GetInstance()->logger->isHandling(Logger::CRITICAL)) {
            return;
        }

        $args = self::enrichWithUser($args);

        try {
            self::GetInstance()->logger->critical($message, $args);
        } catch (Exception $ex) {
            die('Could not write to Booked log file. Please check your log configuration.');
        }
    }

    /**
     * @param string $message
     * @param mixed $args
     */
    public static function Alert($message, $args = array())
    {
        if (!self::GetInstance()->logger->isHandling(Logger::ALERT)) {
            return;
        }

        $args = self::enrichWithUser($args);

        try {
            self::GetInstance()->logger->alert($message, $args);
        } catch (Exception $ex) {
            die('Could not write to Booked log file. Please check your log configuration.');
        }
    }

    /**
     * @param string $message
     * @param mixed $args
     */
    public static function Emergency($message, $args = array())
    {
        if (!self::GetInstance()->logger->isHandling(Logger::EMERGENCY)) {
            return;
        }

        $args = self::enrichWithUser($args);

        try {
            self::GetInstance()->logger->emergency($message, $args);
        } catch (Exception $ex) {
            die('Could not write to Booked log file. Please check your log configuration.');
        }
    }

    /**
     * @static
     * @param string $message
     * @param mixed $args
     * @return void
     */
    public static function Sql($message)
    {
        try {
            if (!self::GetInstance()->sqlLogger->isHandling(Logger::DEBUG)) {
                return;
            }
            self::GetInstance()->sqlLogger->debug($message);
        } catch (Exception $ex) {
        }
    }

    /**
     * @return bool
     */
    public static function DebugEnabled()
    {
        return self::GetInstance()->logger->isHandling(Logger::DEBUG);
    }

    private function GetLevelFromConfig(?string $level): int
    {
        return match (strtolower($level)) {
            "debug" => Logger::DEBUG,
            "info" => Logger::INFO,
            "notice" => Logger::NOTICE,
            "warning" => Logger::WARNING,
            "error" => Logger::ERROR,
            "critical" => Logger::CRITICAL,
            "alert" => Logger::ALERT,
            "emergency" => Logger::EMERGENCY,
            default => Logger::ERROR,
        };
    }

    /**
     * @param mixed $args
     * @return mixed
     */
    private static function enrichWithUser(mixed $args): mixed
    {
        $args['userId'] = null;
        try {
            if (ServiceLocator::GetServer()->IsSessionStarted()) {
                $user = ServiceLocator::GetServer()->GetUserSession();
                $args['userId'] = $user->UserId;
            }
        } catch (Throwable $ex) {
        }

        try {
            if (isset($args['exception']) && method_exists($args['exception'], 'getTraceAsString')) {
                $args['file'] = $args['exception']->getFile();
                $args['line'] = $args['exception']->getLine();
                $args['message'] = $args['exception']->getMessage();
                $args['exception'] = $args['exception']->getTraceAsString();
            }
        } catch (Throwable $ex) {
        }

        return $args;
    }
}

class NullLogger extends Logger
{
    public function __construct()
    {
        parent::__construct("null");
    }

    public function log($level, $message, array $context = []): void
    {
    }

    public function debug($message, array $context = []): void
    {
    }

    public function info($message, array $context = []): void
    {
    }

    public function warning($message, array $context = []): void
    {
    }

    public function notice($message, array $context = []): void
    {
    }

    public function alert($message, array $context = []): void
    {
    }

    public function emergency($message, array $context = []): void
    {
    }

    public function critical($message, array $context = []): void
    {
    }

    public function error($message, array $context = []): void
    {
    }
}