<?php

namespace TCK\Odbc;

use Illuminate\Database\Connectors\Connector;
use Illuminate\Database\Connectors\ConnectorInterface;
use Illuminate\Support\Arr;
use PDO;

class ODBCConnector extends Connector implements ConnectorInterface {

	/**
	 * Establish a database connection.
	 *
	 * @param array $config
	 * @return \Doctrine\DBAL\Driver\PDOConnection|PDO
	 * @internal param array $options
	 */
	public function connect(array$config)
	{
		$options = $this->getOptions($config);

		$dsn = Arr::get($config, 'dsn');

		return $this->createConnection($dsn, $config, $options);
	}
} 
