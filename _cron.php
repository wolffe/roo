<?php
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
set_time_limit(300);

include('includes/functions.php');

$sql = "SELECT domain_id, domain_url, domain_owner FROM mod_domains";
$stmt = $db->prepare($sql);
$stmt->execute();
$resultsMain = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($resultsMain as $rowMain) {
    $url = $rowMain['domain_url'];
    $author = $rowMain['domain_owner'];
    $time = date('Y-m-d H:i:s');
    $up = pinger($url);

    $sql = "SELECT * FROM uptime WHERE linkID='" .  $rowMain['domain_id'] . "' LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($results as $row) {
        $linkLastCheck = $row['linkLastCheck'];
    }

    $sql = "INSERT INTO uptime (linkID, linkLastCheck, linkStatus) VALUES ('" .  $rowMain['domain_id'] . "', '" . $time . "', '" . $up . "')";
    $stmt = $db->prepare($sql);
    $stmt->execute();

    if($up == 0) {
        $sql = "SELECT email FROM user WHERE userid='" .  $author . "' LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($results as $row) {
            $headers   = array();
            $headers[] = "MIME-Version: 1.0";
            $headers[] = "Content-type: text/html; charset=iso-8859-1";
            $headers[] = "From: kanga.roo.ie <noreply@roo.ie>";

            $message = 'Your site - ' . $url . ' - is down!';

            mail($row['email'], 'kanga.roo.ie downtime alert!', $message, implode("\r\n", $headers));
        }
        
    }
}
?>
