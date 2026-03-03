<?php
$message = "";
$messageType = "";

// Zpracování POST požadavku
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["new_interest"])) {
    $newInterest = trim($_POST["new_interest"]);
    
    // Ověření, že pole není prázdné
    if (empty($newInterest)) {
        $message = "Pole nesmí být prázdné.";
        $messageType = "error";
    } else {
        // Načtení existujících zájmů
        $profilePath = "profile.json";
        if (!file_exists($profilePath)) {
            $data = ["interests" => []];
        } else {
            $jsonContent = file_get_contents($profilePath);
            $data = json_decode($jsonContent, true);
        }
        
        // Ověření správnosti JSON
        if (!is_array($data) || !isset($data["interests"])) {
            $data = ["interests" => []];
        }
        
        // Kontrola duplicit (case-insensitive)
        $existingInterests = array_map("strtolower", $data["interests"]);
        if (in_array(strtolower($newInterest), $existingInterests)) {
            $message = "Tento zájem už existuje.";
            $messageType = "error";
        } else {
            // Přidání nového zájmu
            $data["interests"][] = $newInterest;
            
            // Uložení do JSON
            if (file_put_contents($profilePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
                $message = "Zájem byl úspěšně přidán.";
                $messageType = "success";
            } else {
                $message = "Chyba při ukládání souboru.";
                $messageType = "error";
            }
        }
    }
}

// Načtení zájmů pro zobrazení
$profilePath = "profile.json";
$interests = [];
if (file_exists($profilePath)) {
    $jsonContent = file_get_contents($profilePath);
    $data = json_decode($jsonContent, true);
    if (is_array($data) && isset($data["interests"])) {
        $interests = $data["interests"];
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Profil 4.0</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>IT Profil 4.0</h1>
        <p class="subtitle">Spravuj své zájmy a dovednosti</p>
        
        <!-- Zobrazení hlášky -->
        <?php if (!empty($message)): ?>
            <div class="message <?php echo htmlspecialchars($messageType); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulář pro přidání zájmu -->
        <form method="POST" class="form-container">
            <h2>Přidat nový zájem</h2>
            <div class="form-group">
                <input 
                    type="text" 
                    name="new_interest" 
                    placeholder="např. Python, Design, DevOps..." 
                    required
                    class="input-field"
                >
            </div>
            <button type="submit" class="btn-submit">Přidat zájem</button>
        </form>
        
        <!-- Zobrazení existujících zájmů -->
        <div class="interests-container">
            <h2>Tvoje zájmy</h2>
            <?php if (!empty($interests)): ?>
                <ul class="interests-list">
                    <?php foreach ($interests as $interest): ?>
                        <li class="interest-item">
                            <?php echo htmlspecialchars($interest); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="no-interests">Nemáš zatím žádné zájmy. Přidej si první!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
