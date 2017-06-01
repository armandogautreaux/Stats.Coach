<?php

namespace Modules;

// http://php.net/manual/en/function.debug-backtrace.php

class ErrorCatcher
{
    private $report;

    public function __construct( )
    {
        $closure = function () { $this->generateErrorLog(); };
        set_error_handler($closure);
        set_exception_handler($closure);
    }

    private function generateErrorLog()
    {
        if ( 0 == error_reporting () ) return null;

        // Open the file to get existing content
        ob_start( );
        print file_get_contents( ERROR_LOG ) . PHP_EOL;
        print $this->generateCallTrace( ) . PHP_EOL;
        echo $output = ob_get_contents( );
        ob_end_clean( );
        // Write the contents back to the file
        file_put_contents( ERROR_LOG, $output );
        // startApplication();
        exit(1);
    }

    private function generateCallTrace()
    {
        $e = new \Exception( );
        
        ob_start( );
        $trace = explode( "\n", $e->getTraceAsString() );
        // reverse array to make steps line up chronologically
        $trace = array_reverse( $trace );
        array_shift( $trace ); // remove {main}
        array_pop( $trace ); // remove call to this method
        array_pop( $trace ); // remove call to this method

        $length = count( $trace );
        $result = array( );

        for ( $i = 0; $i < $length; $i++ ) {
            $result[] = ($i + 1) . ')' . substr( $trace[$i], strpos( $trace[$i], ' ' ) ) . PHP_EOL;
            print PHP_EOL;
            // replace '#someNum' with '$i)', set the right ordering
        }

        print "\t" . implode( "\n\t", $result );

        $output = ob_get_contents( );
        ob_end_clean( );
        return $this->report = $output;
    }

    public static function reporting($level = 0)
    {
        $error = function () { new ErrorCatcher(); };

        error_reporting( $level );
        ini_set( 'display_errors', ($level == 0 ? 0 : 1) );
        set_error_handler( $error );
        set_exception_handler( $error );
    }

    // TODO - add java tags/ make redirect work
    private function redirect()
    {
        print '<meta http-equiv="refresh" content="0;url= ' . SITE_ROOT . '"/>';
        die(0);
    }

}
