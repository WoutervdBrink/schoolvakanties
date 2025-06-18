<?php

require_once __DIR__ . '/../vendor/autoload.php';

$path = basename($_SERVER['REQUEST_URI']);

$url = ($_SERVER['HTTPS'] ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $path . '/ical.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Schoolvakanties</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<div class="container">
    <h1>Schoolvakanties</h1>

    <p>Alle Nederlandse schoolvakanties automatisch in je agenda.</p>

    <p>Deze tool publiceert een iCal-agenda met daarin <em>alle</em> Nederlandse schoolvakanties. De regionale vakanties
        worden op een slimme manier samengevoegd, waardoor je niet meerdere events per vakantie in je agenda krijgt,
        maar wel kunt zien wanneer welke regio aan de beurt is.</p>

    <p>Op zoek naar een tool die schoolvakanties voor één regio publiceert?
        Probeer <a href="https://schoolvakanties.vercel.app/" target="_blank">deze</a> eens.</p>

    <p>Op zoek naar een overzicht van alle vakanties per regio? Probeer <a href="table.php">deze pagina</a> eens.</p>

    <div class="input-group mb-3">
        <span class="input-group-text">iCal URL:</span>
        <input type="text" class="form-control" aria-label="iCal URL" id="url_field" readonly disabled
               value="<?= htmlentities($url) ?>">
        <button type="button" class="btn btn-primary" id="copy_button">
            <i class="bi bi-copy"></i>
            <span id="clipboard_status">
                    Kopiëren
                </span>
        </button>
    </div>

    <p><a href="https://github.com/WoutervdBrink/schoolvakanties" target="_blank">Broncode</a></p>
</div>

<script>
    const clipboardStatus = document.getElementById('clipboard_status');
    const urlField = document.getElementById('url_field');
    const copyButton = document.getElementById('copy_button');
    let clipboardStatusTimeout = 0;

    copyButton.addEventListener('click', async () => {
        clearTimeout(clipboardStatusTimeout);

        try {
            await navigator.clipboard.writeText(urlField.value);
            clipboardStatus.textContent = 'Gekopieerd!';
        } catch (e) {
            clipboardStatus.textContent = 'Kopiëren mislukt';
        } finally {
            clipboardStatusTimeout = setTimeout(() => clipboardStatus.textContent = 'Kopiëren', 500);
        }
    });
</script>
</body>
</html>