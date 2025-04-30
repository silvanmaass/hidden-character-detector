<?php
// Hidden Character Detector Tool
// Erkennt und markiert versteckte Zeichen in einem Text

// Definiere eine Funktion zum Erkennen und Markieren versteckter Zeichen
function detectHiddenCharacters($text) {
    // Speichere das Original für den Vergleich
    $originalText = $text;
    
    // Array mit versteckten/speziellen Zeichen und ihren Namen
    $hiddenChars = [
        // Zero-width characters
        "\u{200B}" => "ZERO WIDTH SPACE (U+200B)",
        "\u{200C}" => "ZERO WIDTH NON-JOINER (U+200C)",
        "\u{200D}" => "ZERO WIDTH JOINER (U+200D)",
        "\u{FEFF}" => "ZERO WIDTH NO-BREAK SPACE (U+FEFF)",
        
        // Andere nicht druckbare Zeichen
        "\u{00A0}" => "NON-BREAKING SPACE (U+00A0)",
        "\u{2060}" => "WORD JOINER (U+2060)",
        "\u{2061}" => "FUNCTION APPLICATION (U+2061)",
        "\u{2062}" => "INVISIBLE TIMES (U+2062)",
        "\u{2063}" => "INVISIBLE SEPARATOR (U+2063)",
        "\u{2064}" => "INVISIBLE PLUS (U+2064)",
        
        // Steuerzeichen
        "\u{0000}" => "NULL (U+0000)",
        "\u{0001}" => "START OF HEADING (U+0001)",
        "\u{0002}" => "START OF TEXT (U+0002)",
        "\u{0003}" => "END OF TEXT (U+0003)",
        "\u{0004}" => "END OF TRANSMISSION (U+0004)",
        "\u{0005}" => "ENQUIRY (U+0005)",
        "\u{0006}" => "ACKNOWLEDGE (U+0006)",
        "\u{0007}" => "BELL (U+0007)",
        "\u{0008}" => "BACKSPACE (U+0008)",
        "\u{0009}" => "HORIZONTAL TAB (U+0009)",
        "\u{000A}" => "LINE FEED (U+000A)",
        "\u{000B}" => "VERTICAL TAB (U+000B)",
        "\u{000C}" => "FORM FEED (U+000C)",
        "\u{000D}" => "CARRIAGE RETURN (U+000D)",
        "\u{000E}" => "SHIFT OUT (U+000E)",
        "\u{000F}" => "SHIFT IN (U+000F)",
        "\u{0010}" => "DATA LINK ESCAPE (U+0010)",
        "\u{0011}" => "DEVICE CONTROL 1 (U+0011)",
        "\u{0012}" => "DEVICE CONTROL 2 (U+0012)",
        "\u{0013}" => "DEVICE CONTROL 3 (U+0013)",
        "\u{0014}" => "DEVICE CONTROL 4 (U+0014)",
        "\u{0015}" => "NEGATIVE ACKNOWLEDGE (U+0015)",
        "\u{0016}" => "SYNCHRONOUS IDLE (U+0016)",
        "\u{0017}" => "END OF TRANSMISSION BLOCK (U+0017)",
        "\u{0018}" => "CANCEL (U+0018)",
        "\u{0019}" => "END OF MEDIUM (U+0019)",
        "\u{001A}" => "SUBSTITUTE (U+001A)",
        "\u{001B}" => "ESCAPE (U+001B)",
        "\u{001C}" => "FILE SEPARATOR (U+001C)",
        "\u{001D}" => "GROUP SEPARATOR (U+001D)",
        "\u{001E}" => "RECORD SEPARATOR (U+001E)",
        "\u{001F}" => "UNIT SEPARATOR (U+001F)",
        "\u{007F}" => "DELETE (U+007F)",
        
        // Bidirektionale Steuerzeichen
        "\u{061C}" => "ARABIC LETTER MARK (U+061C)",
        "\u{2066}" => "LEFT-TO-RIGHT ISOLATE (U+2066)",
        "\u{2067}" => "RIGHT-TO-LEFT ISOLATE (U+2067)",
        "\u{2068}" => "FIRST STRONG ISOLATE (U+2068)",
        "\u{2069}" => "POP DIRECTIONAL ISOLATE (U+2069)",
        "\u{202A}" => "LEFT-TO-RIGHT EMBEDDING (U+202A)",
        "\u{202B}" => "RIGHT-TO-LEFT EMBEDDING (U+202B)",
        "\u{202C}" => "POP DIRECTIONAL FORMATTING (U+202C)",
        "\u{202D}" => "LEFT-TO-RIGHT OVERRIDE (U+202D)",
        "\u{202E}" => "RIGHT-TO-LEFT OVERRIDE (U+202E)",
        
        // Formatierungszeichen
        "\u{180E}" => "MONGOLIAN VOWEL SEPARATOR (U+180E)",
        "\u{200E}" => "LEFT-TO-RIGHT MARK (U+200E)",
        "\u{200F}" => "RIGHT-TO-LEFT MARK (U+200F)",
    ];
    
    // Zähle alle versteckten Zeichen
    $foundChars = [];
    $positions = [];
    
    foreach ($hiddenChars as $char => $name) {
        // Finde alle Positionen dieses Zeichens im Text
        $offset = 0;
        while (($pos = mb_strpos($text, $char, $offset, 'UTF-8')) !== false) {
            $positions[] = [
                'pos' => $pos,
                'char' => $char,
                'name' => $name
            ];
            $offset = $pos + 1;
            
            // Zähle die Anzahl dieses Zeichentyps
            if (!isset($foundChars[$name])) {
                $foundChars[$name] = 1;
            } else {
                $foundChars[$name]++;
            }
        }
    }
    
    // Sortiere die Positionen aufsteigend nach Position
    usort($positions, function($a, $b) {
        return $a['pos'] <=> $b['pos'];
    });
    
    // Erstelle eine HTML-Ausgabe des Textes mit hervorgehobenen versteckten Zeichen
    $markedText = '';
    $lastPos = 0;
    
    foreach ($positions as $item) {
        // Füge Text vor dem versteckten Zeichen hinzu
        $markedText .= htmlspecialchars(mb_substr($text, $lastPos, $item['pos'] - $lastPos, 'UTF-8'));
        
        // Füge das versteckte Zeichen hervorgehoben hinzu
        $markedText .= '<span class="hidden-char" title="' . $item['name'] . '">■</span>';
        
        $lastPos = $item['pos'] + mb_strlen($item['char'], 'UTF-8');
    }
    
    // Füge den Rest des Textes hinzu
    $markedText .= htmlspecialchars(mb_substr($text, $lastPos, null, 'UTF-8'));
    
    return [
        'markedText' => $markedText,
        'foundChars' => $foundChars,
        'totalHidden' => count($positions)
    ];
}

// Verarbeite das Formular, wenn es abgesendet wurde
$result = null;
$submitText = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['text'])) {
    $submitText = $_POST['text'];
    $result = detectHiddenCharacters($submitText);
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hidden Character Detector</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            color: #333;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .container {
            background-color: #f9f9f9;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        textarea {
            width: 100%;
            min-height: 150px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 15px;
            resize: vertical;
            font-family: monospace;
        }
        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #2980b9;
        }
        .result {
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .result-text {
            background-color: #fff;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 15px;
            white-space: pre-wrap;
            word-break: break-word;
            font-family: monospace;
        }
        .hidden-char {
            background-color: #ff6b6b;
            color: white;
            padding: 0 4px;
            border-radius: 3px;
            margin: 0 2px;
            font-weight: bold;
            cursor: help;
        }
        .char-list {
            margin-top: 15px;
            background-color: #eaf2f8;
            padding: 10px;
            border-radius: 4px;
        }
        .char-list ul {
            list-style-type: none;
            padding-left: 0;
        }
        .char-list li {
            margin-bottom: 5px;
            padding: 5px 10px;
            background-color: #fff;
            border-radius: 3px;
            border-left: 3px solid #3498db;
        }
        .no-hidden {
            color: #27ae60;
            font-weight: bold;
        }
        .summary {
            font-weight: bold;
            margin-bottom: 10px;
            color: <?php echo isset($result) && $result['totalHidden'] > 0 ? '#e74c3c' : '#27ae60'; ?>;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Hidden Character Detector</h1>
        <h2>Was macht dieses Tool?</h2>
        <p>Dieses Tool hilft dir, versteckte und unsichtbare Zeichen in Texten zu finden und sichtbar zu machen. Solche Zeichen sind normalerweise nicht sichtbar, können aber in Texten vorhanden sein und manchmal Probleme verursachen.</p>
        
        <h3>Wozu ist das nützlich?</h3>
        <ul>
            <li><strong>Fehlerbehebung:</strong> Finde unsichtbare Zeichen, die Formatierungsprobleme oder Bugs in deinem Code verursachen</li>
            <li><strong>Sicherheit:</strong> Entdecke versteckte Zeichen, die in Phishing-Versuchen oder zur Manipulation von URLs verwendet werden könnten</li>
            <li><strong>KI-Erkennung:</strong> Identifiziere mögliche unsichtbare Wasserzeichen in KI-generierten Texten</li>
            <li><strong>Datenbereinigung:</strong> Identifiziere und entferne unerwünschte Zeichen aus importierten Texten oder Datenbanken</li>
            <li><strong>Qualitätssicherung:</strong> Überprüfe Texte vor der Veröffentlichung auf unbeabsichtigte versteckte Zeichen</li>
        </ul>
        
        <h3>So funktioniert es:</h3>
        <ol>
            <li>Kopiere deinen Text in das Feld unten</li>
            <li>Klicke auf "Text prüfen"</li>
            <li>Das Tool markiert alle versteckten Zeichen mit einem roten Quadrat (■)</li>
            <li>Fahre mit der Maus über ein markiertes Zeichen, um dessen Unicode-Information zu sehen</li>
            <li>Unten wird eine Liste mit allen gefundenen versteckten Zeichen und deren Häufigkeit angezeigt</li>
        </ol>
        
        <p>Jetzt ausprobieren:</p>
        
        <form method="post">
            <textarea name="text" placeholder="Text hier einfügen..."><?php echo htmlspecialchars($submitText); ?></textarea>
            <button type="submit">Text prüfen</button>
        </form>
        
        <?php if ($result): ?>
        <div class="result">
            <div class="summary">
                <?php if ($result['totalHidden'] > 0): ?>
                    Es wurden <?php echo $result['totalHidden']; ?> versteckte Zeichen gefunden!
                <?php else: ?>
                    <span class="no-hidden">Keine versteckten Zeichen gefunden!</span>
                <?php endif; ?>
            </div>
            
            <h3>Text mit markierten versteckten Zeichen:</h3>
            <div class="result-text"><?php echo $result['markedText']; ?></div>
            
            <?php if (count($result['foundChars']) > 0): ?>
                <div class="char-list">
                    <h3>Gefundene versteckte Zeichen:</h3>
                    <ul>
                    <?php foreach ($result['foundChars'] as $name => $count): ?>
                        <li><?php echo htmlspecialchars($name); ?>: <?php echo $count; ?> mal</li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px; font-size: 0.9em; color: #7f8c8d;">
            <h3>Was sind versteckte Zeichen?</h3>
            <p>Versteckte Zeichen sind Bestandteile des Unicode-Standards, die im Text normalerweise nicht sichtbar sind, aber dennoch vorhanden sein können. Sie dienen unterschiedlichen Zwecken:</p>
            
            <ul>
                <li><strong>Zero-Width-Zeichen</strong> (U+200B, U+200C, U+200D, usw.): Unsichtbare Zeichen ohne Breite, die zur Steuerung der Textdarstellung verwendet werden</li>
                <li><strong>Steuerzeichen</strong> (ASCII 0-31): Nicht-druckbare Zeichen, die ursprünglich für die Steuerung von Geräten wie Druckern verwendet wurden</li>
                <li><strong>Formatierungszeichen</strong>: Beeinflussen die Formatierung und das Layout von Text</li>
                <li><strong>Bidirektionale Steuerzeichen</strong>: Steuern die Textrichtung für Sprachen, die von rechts nach links geschrieben werden</li>
                <li><strong>Nicht-druckbare Whitespace-Zeichen</strong>: Verschiedene Arten von Leerzeichen, die sich von normalen Leerzeichen unterscheiden</li>
            </ul>
            
            <h3>Häufige Probleme durch versteckte Zeichen:</h3>
            <ul>
                <li>Text verhält sich beim Kopieren und Einfügen seltsam</li>
                <li>Unerklärliche Formatierungsprobleme in Dokumenten</li>
                <li>Fehler bei der Verarbeitung von Daten</li>
                <li>Sicherheitsprobleme durch absichtlich manipulierte URLs oder Texte</li>
                <li>Probleme beim Parsen von Daten oder Code</li>
            </ul>
            
            <h3>Versteckte Zeichen in KI-generierten Texten:</h3>
            <p>Wusstest du? Einige KI-Chatbots und Textgeneratoren fügen absichtlich versteckte Zeichen (wie Zero-Width-Spaces oder andere nicht sichtbare Unicode-Zeichen) in ihre Ausgaben ein. Diese dienen als eine Art "unsichtbares Wasserzeichen", um KI-generierte Inhalte nachträglich identifizieren zu können. Mit diesem Tool kannst du überprüfen, ob Texte solche versteckten Markierungen enthalten.</p>
            
            <p><strong>Anwendungsfälle:</strong></p>
            <ul>
                <li>Überprüfen, ob ein Text möglicherweise von einer KI generiert wurde</li>
                <li>Identifizieren von versteckten Markierungen oder Wasserzeichen in Texten</li>
                <li>Entfernen solcher Markierungen, falls gewünscht</li>
            </ul>
            
            <p><strong>Tipp:</strong> Wenn du die gefundenen versteckten Zeichen entfernen möchtest, kannst du den Text nach der Analyse korrigieren oder spezielle Text-Editoren verwenden, die versteckte Zeichen anzeigen können.</p>
        </div>
    </div>
</body>
</html>