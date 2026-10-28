<script>
    try {
        var novaAlertMatch = document.cookie.match(/(?:^|; )nova_alerts=(granted|denied)/);
        var novaAlertChoice = novaAlertMatch ? novaAlertMatch[1] : '';
        var novaAlertPerm = (typeof Notification === 'undefined') ? 'unsupported' : Notification.permission;
        var novaAlertReady = novaAlertChoice === 'denied' || novaAlertPerm === 'unsupported' || (novaAlertChoice === 'granted' && novaAlertPerm === 'granted');
        document.documentElement.classList.add(novaAlertReady ? 'nova-alerts-set' : 'nova-alerts-ask');
    } catch (error) {}
</script>
