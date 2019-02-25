<br clear="all">
</div>
<div id="footer">
    <?php if(isset($_SESSION['username'])) { ?>
        Logged in as <strong><?php echo $_SESSION['username']; ?></strong> | 
    <?php } ?>
    <a href="system-changelog.php">Release notes</a> | 
    <?php echo $org_version; ?>
</div>
</body>
</html>
