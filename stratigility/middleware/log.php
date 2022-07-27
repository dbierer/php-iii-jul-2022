<?php
/**
 * Log Middleware
 */
namespace stratigility\middleware;
return [
    // middleware: writes to a log file; does not return a response
    'log' => [
        'path' => FALSE,
        'func' => function ($req, $handler) {
            $text = sprintf('%20s : %10s : %16s : %s' . PHP_EOL,
                date('Y-m-d H:i:s'),
                $req->getUri()->getPath(),
                ($req->getHeaders()['accept'][0] ?? 'N/A'),
                ($req->getServerParams()['REMOTE_ADDR']) ?? 'Command Line');
            file_put_contents(LOG_FILE, $text, FILE_APPEND);
            return $handler->handle($req);
        }
    ],
];

