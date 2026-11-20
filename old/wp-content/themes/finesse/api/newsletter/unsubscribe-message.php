<html>
<body>
<?php global $unsubscription_success;?>
<?php if ($unsubscription_success) {
    echo '<h1>You have been successfully unsubscribed.</h1>';
} else {
    echo '<h1>You have been already unsubscribed.</h1>';
}
?>

<p>Redirecting to the <?php echo '<a href="' . site_url() . '">home page</a>'; ?> in 3 seconds....</p>

<script type="text/javascript">setTimeout(function () {
<?php $redirect_URL = site_url(); echo 'window.location.href="' . $redirect_URL . '";'; ?>
}, 3000);</script>

</body>
</html>
