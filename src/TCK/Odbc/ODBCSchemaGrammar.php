<?php

namespace TCK\Odbc;

use Illuminate\Support\Fluent;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\Grammar;

class ODBCSchemaGrammar extends Grammar {

	/**
	 * The keyword identifier wrapper format.
	 *
	 * @var string
	 */
	protected $wrapper = '%s';

	/**
	 * The possible column modifiers.
	 *
	 * @var array
	 */
	protected $modifiers = ['Unsigned', 'Nullable', 'Default', 'Increment'];

	/**
	 * Compile the query to determine if a table exists.
	 */
	public function compileTableExists(): string
	{
		return 'select * from information_schema.tables where table_schema = ? and table_name = ?';
	}

    /**
     * Compile a create table command.
     */
	public function compileCreate(Blueprint $blueprint, Fluent $command): string
	{
		$columns = implode(', ', $this->getColumns($blueprint));

		return 'create table ' . $this->wrapTable($blueprint) . " ($columns)";
	}

    /**
     * Compile a create table command.
     */
	public function compileAdd(Blueprint $blueprint, Fluent $command): string
    {
		$table = $this->wrapTable($blueprint);

		$columns = $this->prefixArray('add', $this->getColumns($blueprint));

		return 'alter table ' . $table . ' ' . implode(', ', $columns);
	}

	/**
	 * Compile a primary key command.
	 */
	public function compilePrimary(Blueprint $blueprint, Fluent $command): string
    {
		$command->name(null);

		return $this->compileKey($blueprint, $command, 'primary key');
	}

	/**
	 * Compile an index creation command.
	 *
	 * @param  string $type
	 */
	protected function compileKey(Blueprint $blueprint, Fluent $command, $type): string
	{
		$columns = $this->columnize($command->columns);

		$table = $this->wrapTable($blueprint);

		return "alter table {$table} add {$type} {$command->index}($columns)";
	}

	/**
	 * Compile a unique key command.
	 */
	public function compileUnique(Blueprint $blueprint, Fluent $command): string
	{
		return $this->compileKey($blueprint, $command, 'unique');
	}

	/**
	 * Compile a plain index key command.
	 */
	public function compileIndex(Blueprint $blueprint, Fluent $command): string
	{
		return $this->compileKey($blueprint, $command, 'index');
	}

	/**
	 * Compile a drop table command.
	 */
	public function compileDrop(Blueprint $blueprint, Fluent $command): string
    {
		return 'drop table ' . $this->wrapTable($blueprint);
	}

	/**
	 * Compile a drop table (if exists) command.
	 */
	public function compileDropIfExists(Blueprint $blueprint, Fluent $command): string
    {
		return 'drop table if exists ' . $this->wrapTable($blueprint);
	}

	/**
	 * Compile a drop column command.
	 */
	public function compileDropColumn(Blueprint $blueprint, Fluent $command): string
    {
		$columns = $this->prefixArray('drop', $this->wrapArray($command->columns));

		$table = $this->wrapTable($blueprint);

		return 'alter table ' . $table . ' ' . implode(', ', $columns);
	}

	/**
	 * Compile a drop primary key command.
	 */
	public function compileDropPrimary(Blueprint $blueprint, Fluent $command): string
    {
		return 'alter table ' . $this->wrapTable($blueprint) . ' drop primary key';
	}

	/**
	 * Compile a drop unique key command.
	 */
	public function compileDropUnique(Blueprint $blueprint, Fluent $command): string
    {
		$table = $this->wrapTable($blueprint);

		return "alter table {$table} drop index {$command->index}";
	}

	/**
	 * Compile a drop index command.
	 */
	public function compileDropIndex(Blueprint $blueprint, Fluent $command): string
    {
		$table = $this->wrapTable($blueprint);

		return "alter table {$table} drop index {$command->index}";
	}

	/**
	 * Compile a drop foreign key command.
	 */
	public function compileDropForeign(Blueprint $blueprint, Fluent $command): string
    {
		$table = $this->wrapTable($blueprint);

		return "alter table {$table} drop foreign key {$command->index}";
	}

	/**
	 * Compile a rename table command.
	 */
	public function compileRename(Blueprint $blueprint, Fluent $command): string
    {
		$from = $this->wrapTable($blueprint);

		return "rename table {$from} to " . $this->wrapTable($command->to);
	}

	/**
	 * Create the column definition for a string type.
	 */
	protected function typeString(Fluent $column): string
    {
		return "varchar({$column->length})";
	}

	/**
	 * Create the column definition for a text type.
	 */
	protected function typeText(Fluent $column): string
    {
		return 'text';
	}

	/**
	 * Create the column definition for a integer type.
	 */
	protected function typeInteger(Fluent $column): string
    {
		return 'int';
	}

	/**
	 * Create the column definition for a float type.
	 */
	protected function typeFloat(Fluent $column): string
    {
		return "float({$column->total}, {$column->places})";
	}

	/**
	 * Create the column definition for a decimal type.
	 */
	protected function typeDecimal(Fluent $column): string
    {
		return "decimal({$column->total}, {$column->places})";
	}

	/**
	 * Create the column definition for a boolean type.
	 */
	protected function typeBoolean(Fluent $column): string
    {
		return 'tinyint';
	}

	/**
	 * Create the column definition for a enum type.
	 */
	protected function typeEnum(Fluent $column): string
    {
		return "enum('" . implode("', '", $column->allowed) . "')";
	}

	/**
	 * Create the column definition for a date type.
	 */
	protected function typeDate(Fluent $column): string
    {
		return 'date';
	}

	/**
	 * Create the column definition for a date-time type.
	 */
	protected function typeDateTime(Fluent $column): string
    {
		return 'datetime';
	}

	/**
	 * Create the column definition for a time type.
	 */
	protected function typeTime(Fluent $column): string
    {
		return 'time';
	}

	/**
	 * Create the column definition for a timestamp type.
	 */
	protected function typeTimestamp(Fluent $column): string
    {
		return 'timestamp default 0';
	}

	/**
	 * Create the column definition for a binary type.
	 */
	protected function typeBinary(Fluent $column): string
    {
		return 'blob';
	}

	/**
	 * Get the SQL for an unsigned column modifier.
	 *
	 * @return string|null
	 */
	protected function modifyUnsigned(Blueprint $blueprint, Fluent $column)
	{
		if ($column->type == 'integer' and $column->unsigned)
		{
			return ' unsigned';
		}

        return null;
	}

	/**
	 * Get the SQL for a nullable column modifier.
	 */
	protected function modifyNullable(Blueprint $blueprint, Fluent $column): ?string
    {
		return $column->nullable ? ' null' : ' not null';
	}

	/**
	 * Get the SQL for a default column modifier.
	 */
	protected function modifyDefault(Blueprint $blueprint, Fluent $column): ?string
    {
		if (! is_null($column->default))
		{
			return " default '" . $this->getDefaultValue($column->default) . "'";
		}

        return null;
	}

	/**
	 * Get the SQL for an auto-increment column modifier.
	 */
	protected function modifyIncrement(Blueprint $blueprint, Fluent $column): ?string
    {
		if ($column->type == 'integer' and $column->autoIncrement) {
			return ' auto_increment primary key';
		}

        return null;
	}
}
