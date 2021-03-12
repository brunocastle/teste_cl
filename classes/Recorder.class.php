<?php

class Recorder {

	static private $message = [];

	/**
	 * @param $messageType string
	 * @param $record string
	 */
	public static function record( $messageType, $record ) {
		
		if ( !isset( self::$message[ $messageType ] ) )
			Recorder::$message[ $messageType ] = [];

		array_push( Recorder::$message[ $messageType ], $record );

	}

	public static function print() {

		foreach ( Recorder::$message as $info ) {

			echo key( Recorder::$message ) . ':';
			
			foreach ( $info as $detail ) {

				echo '<br/>' . $detail;

			}

			next( Recorder::$message );

		}
	
	}

	public static function printAndErase() {

		Recorder::print();

		Recorder::$message = [];

	}

}