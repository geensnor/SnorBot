<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function getCurrentTourLocation(): string
{
    // Load the currentTour.yaml file
    $currentTourYaml = file_get_contents('https://raw.githubusercontent.com/geensnor/Geensnor-Tourpoule-Data/main/data/currentTour.yaml');

    // Parse the YAML data using a custom function
    $currentTourData = parseYaml($currentTourYaml);

    // Get the currentTourLocation from the data
    $currentTourLocation = $currentTourData['currentTourLocation'];

    // Output the tour location
    return $currentTourLocation;
}

function parseYaml(string $yamlString): array
{
    $lines = explode("\n", $yamlString);
    $data = [];
    $currentKey = null;
    $currentValue = null;

    foreach ($lines as $line) {
        $trimmedLine = trim($line);
        if ($trimmedLine === '') {
            continue;
        }

        if (strpos($trimmedLine, ':') !== false) {
            $parts = explode(':', $trimmedLine, 2);
            $key = trim($parts[0]);
            $value = trim($parts[1]);
            $data[$key] = $value;
            $currentKey = $key;
            $currentValue = $value;
        } else {
            if ($currentKey !== null) {
                $data[$currentKey] .= "\n" . $trimmedLine;
                $currentValue .= "\n" . $trimmedLine;
            }
            // Optionally, else: handle unexpected format
        }
    }

    return $data;
}

function getCurrentTourName(): string
{
    $currentTourLocation = getCurrentTourLocation();

    // Construct the URL to the tourConfig.yaml file
    $tourConfigUrl = 'https://raw.githubusercontent.com/geensnor/Geensnor-Tourpoule-Data/main/data' . $currentTourLocation . '/tourConfig.yaml';

    // Load the tourConfig.yaml file
    $tourConfigYaml = file_get_contents($tourConfigUrl);
    $tourConfigData = parseYaml($tourConfigYaml);

    // Get the name of the tour
    if (array_key_exists('name', $tourConfigData)) {
        $tourName = $tourConfigData['name'];
    } else {
        $tourName = 'Unknown';
    }

    // Output the tour name
    return $tourName;
}

$tourLocation = getCurrentTourLocation();
$tourName = getCurrentTourName();
?>

<p>The current tour location is: <?php echo $tourLocation; ?></p>
<p>The current tour name is: <?php echo $tourName; ?></p>