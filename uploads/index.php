<?php
/**
 * Prevent directory indexing
 */
http_response_code(403);
header('Content-Type: text/plain; charset=UTF-8');
exit('403 Forbidden: Direct access to this directory is prohibited.');

