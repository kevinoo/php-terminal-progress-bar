<?php

namespace kevinoo\TerminalProgress;

use DateTime;


/**
 * Print a progress bar in a shell/terminal
*/
class Bar {

	protected static int $time_started = 0;
	protected static int $length_bar = 100;
    protected static string $time_to_finish = 'Calculating time...';

	/**
	 * Format the remaining time
	 * @param	int     $time	The time to format
	 * @return	string
	*/
	protected static function formatTime( int $time ): string {
        return (new DateTime())->setTimestamp($time)->format('G\h i\m s\s');
	}

	/**
	 * Print the progress bar
	 * @param int	$current	The current index
	 * @param int	$max		The max counter
	*/
	public static function printProgress( int $current=0, int $max=0 ): void {

		if( PHP_SAPI !== 'cli' ){
			return;
		}

		if( $current === 1 ){
			$time_elapsed = 0;
			//	Hook, add one microsecond :)
			static::$time_started = hrtime(true) - 1;
		}else{
            $time_elapsed = (hrtime(true) - static::$time_started) / 1e+8;
		}

		$t = ($current / $max) * static::$length_bar;

		if( $t > 100 ){
			$t = 100;
		}

		if( $current > 10 && ($current % 10000 == 0) ){
            $_time_to_finish = (static::$length_bar - $t) * ($time_elapsed / $t);
			static::$time_to_finish = 'Remaning: '. static::formatTime($_time_to_finish);
		}

		if( $t >= 100 ){
            static::$time_to_finish = 'Execution time: '. static::formatTime(microtime(true) - static::$time_started);
		}

		$percent = number_format($t,2,'.','');
		$pr = round($t);
		echo "\r\t{$percent}%\t[". str_repeat('=',$pr) . str_repeat('.',(static::$length_bar-$pr)) ."] {$current} / {$max}  ". static::$time_to_finish ."        ";

		if( $current === $max ){
			echo "\n\n";
		}
	}
}
