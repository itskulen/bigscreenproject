<?php
/**
 * Error Handler Middleware
 * Handles various types of errors and redirects to appropriate error pages
 */

class ErrorHandler
{
    /**
     * Handle 404 Not Found errors
     */
    public static function handle404($message = 'Page not found')
    {
        http_response_code(404);

        // Log the error
        error_log('404 Error: ' . $_SERVER['REQUEST_URI'] . ' - ' . $message);

        // If it's an AJAX request, return JSON
        if (self::isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'error' => true,
                'code' => 404,
                'message' => $message,
            ]);
            exit();
        }

        // Redirect to 404 page
        include_once '404.php';
        exit();
    }

    /**
     * Handle 403 Unauthorized errors
     */
    public static function handle403($message = 'Access denied')
    {
        http_response_code(403);

        // Log the error
        error_log('403 Error: ' . $_SERVER['REQUEST_URI'] . ' - ' . $message);

        // If it's an AJAX request, return JSON
        if (self::isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'error' => true,
                'code' => 403,
                'message' => $message,
            ]);
            exit();
        }

        // Redirect to unauthorized page
        include_once 'unauthorized.php';
        exit();
    }

    /**
     * Handle 500 Internal Server errors
     */
    public static function handle500($message = 'Internal server error')
    {
        http_response_code(500);

        // Log the error
        error_log('500 Error: ' . $_SERVER['REQUEST_URI'] . ' - ' . $message);

        // If it's an AJAX request, return JSON
        if (self::isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'error' => true,
                'code' => 500,
                'message' => $message,
            ]);
            exit();
        }

        // Redirect to 500 page
        include_once '500.php';
        exit();
    }

    /**
     * Check if the current request is an AJAX request
     */
    private static function isAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Validate if a file/page exists
     */
    public static function validatePageExists($page)
    {
        $allowedPages = ['index.php', 'login.php', 'costume_index.php', 'costume_admin.php', 'costume_edit.php', 'mascot_index.php', 'mascot_admin.php', 'mascot_edit.php', 'unauthorized.php', '404.php', '500.php'];

        if (!in_array($page, $allowedPages) || !file_exists($page)) {
            self::handle404('Requested page does not exist');
        }
    }

    /**
     * Validate database connection
     */
    public static function validateDatabase($pdo)
    {
        try {
            $pdo->query('SELECT 1');
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            self::handle500('Database connection failed');
        }
    }

    /**
     * Handle file not found errors
     */
    public static function handleFileNotFound($filename)
    {
        error_log('File not found: ' . $filename);
        self::handle404('Requested file not found: ' . basename($filename));
    }

    /**
     * Handle invalid parameters
     */
    public static function handleInvalidParameter($param, $value = null)
    {
        $message = 'Invalid parameter: ' . $param;
        if ($value !== null) {
            $message .= ' (value: ' . $value . ')';
        }

        error_log($message);
        self::handle404($message);
    }
}

/**
 * Custom error and exception handlers
 */
function customErrorHandler($errno, $errstr, $errfile, $errline)
{
    $error = "Error [$errno]: $errstr in $errfile on line $errline";
    error_log($error);

    // For fatal errors, show 500 page
    if ($errno === E_ERROR || $errno === E_CORE_ERROR || $errno === E_COMPILE_ERROR) {
        ErrorHandler::handle500('A critical error occurred');
    }

    return true;
}

function customExceptionHandler($exception)
{
    $error = 'Uncaught exception: ' . $exception->getMessage() . ' in ' . $exception->getFile() . ' on line ' . $exception->getLine();
    error_log($error);

    ErrorHandler::handle500('An unexpected error occurred');
}

// Set custom error and exception handlers
set_error_handler('customErrorHandler');
set_exception_handler('customExceptionHandler');

// Handle shutdown errors (fatal errors)
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && $error['type'] === E_ERROR) {
        ErrorHandler::handle500('A fatal error occurred');
    }
});
?>
