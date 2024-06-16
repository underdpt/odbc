<?php namespace TCK\Odbc;

use Illuminate\Database\Connection;
use Illuminate\Database\Grammar;

class ODBCConnection extends Connection {

	/**
	 * Get the default query grammar instance.
	 */
	protected function getDefaultQueryGrammar(): Grammar
	{
		$class = config('database.connections.odbc.grammar.query') ?: '\TCK\Odbc\ODBCQueryGrammar';

		return $this->withTablePrefix(new $class());
	}

	/**
	 * Get the default schema grammar instance.
	 */
	protected function getDefaultSchemaGrammar(): Grammar
	{
		$class = config('database.connections.odbc.grammar.schema') ?: '\TCK\Odbc\ODBCSchemaGrammar';

		return $this->withTablePrefix(new $class());
	}
}
