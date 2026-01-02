<?php

// Taulukko, jossa kaikki kysymykset ja oikeat vastaukset
// "k" = kysymysteksti
// "v" = vastausvaihtoehdot
// "oikein" = oikea vastaus
$kysymykset = [
    ["k" => "Mikä yritys valmistaa iPhone-puhelimia?", "v" => ["Apple", "Samsung", "Huawei"], "oikein" => "Apple"],
    ["k" => "Mikä yritys loi Windows-käyttöjärjestelmän?", "v" => ["Google", "Microsoft", "Apple"], "oikein" => "Microsoft"],
    ["k" => "Mikä selaimista on Googlen kehittämä?", "v" => ["Firefox", "Safari", "Chrome"], "oikein" => "Chrome"],
    ["k" => "Minkä valmistajan puhelinmalli on Galaxy?", "v" => ["Samsung", "Nokia", "Xiaomi"], "oikein" => "Samsung"],
    ["k" => "Kuka perusti Microsoftin?", "v" => ["Steve Jobs", "Larry Page", "Bill Gates"], "oikein" => "Bill Gates"],
    ["k" => "Mikä on maailman suosituin videopalvelu?(2025)", "v" => ["YouTube", "Netflix", "Twitch"], "oikein" => "YouTube"],
    ["k" => "Mikä on Applen kannettavan tietokoneen tuotesarja?", "v" => ["iNote", "AirBook", "MacBook"], "oikein" => "MacBook"],
    ["k" => "Mikä on maailman suosituin viestisovellus(2025)?", "v" => ["WhatsApp", "Messenger", "Telegram"], "oikein" => "WhatsApp"],
    ["k" => "Mikä oli ensimmäisen PlayStation-konsolin julkaisuvuosi?", "v" => ["1994", "2004", "2014"], "oikein" => "1994"],
    ["k" => "Mikä on maailman suosituin suoratoistopalvelu musiikille(2025)?", "v" => ["Spotify", "Apple music", "Deezer"], "oikein" => "Spotify"]
];


// Tarkistetaan, onko lomake lähetetty
if ($_SERVER["REQUEST_METHOD"] === "POST") {   
    // siistitään nimimerkki, vain sallitut merkit
    $nimimerkki = trim($_POST["nimimerkki"] ?? '');
    $nimimerkki = preg_replace("/[^a-zA-Z0-9_]/", "", $nimimerkki);
    
    //luetaan vastaukset
    $vastaukset = $_POST["vastaukset"] ?? [];
    $pisteet = 0;                           

    // Käydään kaikki kysymykset ja vaihtoehdot eli index läpi
    foreach ($kysymykset as $index => $kysymys) {
        // tarkastetaan onko valinta tehty ja onko se oikea
        if (isset($vastaukset[$index]) && $vastaukset[$index] === $kysymys["oikein"]) {
            //jos oikea, lisätään piste
            $pisteet++;
        }
    }

        // tarkastetaan pisteet ja tulostetaan viesti pisteiden perusteella
        if ($pisteet === 10) {
        $viesti = ""; 
        } elseif ($pisteet >= 9) {
        $viesti = "Palkinto oli lähellä, kokeile uudestaan!💪";
        } else {
        $viesti = "Voit vielä vähän kertailla tietoja, niin näet palkinnon 😊";
        }

        $dataKansio = __DIR__ . "/data/";
        $tiedosto = $dataKansio . "tietovisa.json";

        $uusi = [
            "aika" => date("Y-m-d H:i"),
            "nimimerkki" => $nimimerkki,
            "pisteet" => $pisteet
        ];

        $nykyinen = [];
        if (file_exists($tiedosto)) {
            $nykyinen = json_decode(file_get_contents($tiedosto), true);
            if (!is_array($nykyinen)) {
                $nykyinen = [];
            }
        }

        $nykyinen[] = $uusi;

        file_put_contents(
            $tiedosto,
            json_encode($nykyinen, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );


    //tulostetaan html näkymä, käytetään php:n heredoc-syntaksia jolla ei tarvitse jokaista echo tulostusta erikseen, vaan tulostetaan koko html sellaisenaan
    echo <<<HTML
    <!DOCTYPE html>
    <html lang="fi">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Joonas Eskelinen</title>

        <!-- Bootstrap, oma tyyli ja fontti -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous"/>
        <link rel="stylesheet" href="style.css">
        <link href="https://fonts.googleapis.com/css2?family=Allura&display=swap" rel="stylesheet">
    </head>

<body>
<!-- Header -->
<header class="hero-header text-white">

  <!-- Otsikko -->
  <div class="hero-content text-center d-flex flex-column justify-content-center align-items-center">
    <div class="container">
      <h1 class="hero-title">Testaa tietosi</h1>
      <h3 class="hero-subtitle">10/10 vastanneille tulostuu palkinto!</h3>
    </div>
  </div>
</header>


HTML;

    // Jos täydet pisteet (10/10) -> näytetään palkintovideo + animaatio
    if ($pisteet === 10) { 
        echo <<<HTML
        <div class="neu-box text-center mt-4">
          <h2>Kiitos, {$nimimerkki}!</h2>
          <h3>Onnittelut, sait täydet pisteet, olet ansainnut palkinnon!</h3>
          <div class="winner-video">
            <iframe 
              src="https://www.youtube.com/embed/1mhSn50MJX0?autoplay=1&rel=0&showinfo=0"
              title="Voittovideo"
              frameborder="0"
              allow="autoplay; encrypted-media"
              allowfullscreen>
            </iframe>
          </div>
        </div>
HTML;

        // luodaan näytölle putoavat palkintoemojit
        echo '<div class="tausta">';
        for ($i = 0; $i < 120; $i++) {
            $vasen = rand(0, 100);
            $viive = rand(0, 100) / 10;
            $kesto = rand(8, 18);
            $koko = rand(18, 30);
            $lap = rand(5, 10) / 10;
            echo "<div class='trophy' style='left: {$vasen}%; animation-delay: {$viive}s; animation-duration: {$kesto}s; font-size: {$koko}px; opacity: {$lap};'>🏆</div>";
        }
        echo '</div>';
    } 
    else {
        echo <<<HTML
    <div class="neu-box-visa text-center mt-4">
      <h2>Kiitos, {$nimimerkki}!</h2>
      <h3>Sait {$pisteet}/10 pistettä.</h3>
      <p>{$viesti}</p>
      <a href="tietovisa.php" class="btn">Pelaa uudestaan</a>
    </div>
  HTML;  
  }

    exit;
}
?>


<!-- tietovisa sivun alkunäkymä ennen vastausten lähetystä -->
<!DOCTYPE html>
<html lang="fi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Joonas Eskelinen</title>

  <!-- Bootstrap, oma tyyli ja fontit -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous"/>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Allura&display=swap" rel="stylesheet">
</head>

<body>
<!-- Header -->
<header class="hero-header text-white">  
<!-- Otsikko -->
  <div class="hero-content text-center d-flex flex-column justify-content-center align-items-center">
    <div class="container">
      <h1 class="hero-title">Testaa tietosi</h1>
      <h3 class="hero-subtitle">10/10 vastanneille tulostuu palkinto!</h3>
    </div>
  </div>
</header>

  <!-- tietovisalomakkeet -->
  <main class="container my-5 pt-hero-gap">
    <form method="POST" action="tietovisa.php">

      <!-- turvallinen nimimerkki -->
      <div class="neu-box-visa text-center mb-4">
      <h4>Kirjoita nimimerkkisi</h4>
      <input type="text" 
         name="nimimerkki" 
         required 
         class="form-control" 
         placeholder="Vain kirjaimet, numerot ja alaviiva" 
         maxlength="20" 
         pattern="[a-zA-Z0-9_]+" 
         title="Vain kirjaimet, numerot ja alaviiva sallittu">
        </div>

      <!-- tulostetaan kysymykset -->
      <?php foreach ($kysymykset as $index => $kysymys): ?>
        <div class="neu-box-visa mb-4">
          <h4><?= ($index + 1) . ". " . htmlspecialchars($kysymys["k"]); ?></h4>
          <?php
          // Kopioidaan vastausvaihtoehdot ja sekoitetaan kysymykset satunnaiseen järjestykseen
          $shuffled = $kysymys["v"];   
          shuffle($shuffled);          
          foreach ($shuffled as $vastaus): ?>
            <label>
              <!--vastauspainike radiobuttonina, sallitaan vain yksi vastaus -->
              <input type="radio" name="vastaukset[<?= $index ?>]" value="<?= htmlspecialchars($vastaus) ?>" required>
              <?= htmlspecialchars($vastaus) ?>
            </label><br>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>

      <!-- lähetyspainike -->
      <div class="text-center">
        <button type="submit" class="btn">Lähetä vastaukset</button>
      </div>
    </form>
  </main>

<!-- Bootstrap JS-->
<script
  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
  crossorigin="anonymous">
</script>


</body>
</html>