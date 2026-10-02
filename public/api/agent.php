<?php
/**
 * AI Agent Integration API Endpoint (Entry Point)
 */

require_once dirname(dirname(__DIR__)) . '/config/config.php';
require_once dirname(dirname(__DIR__)) . '/config/database.php';

// Route request to the AgentController
(new Controllers\AgentController())->handle();
