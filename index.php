<?php
/**
 * StoryMapTeams - Main Entry Point
 *
 * If ?id=X is provided, serves the StoryMap preview
 * Otherwise, could show a landing page or redirect to editor
 */

// Check for ID in query string
$id = isset($_GET['id']) ? basename($_GET['id']) : null;

if ($id) {
    // Serve the preview page with embedded ID
    // This avoids a redirect and keeps the clean URL
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StoryMap Preview</title>
    <!-- StoryMapJS CSS and JS -->
    <link rel="stylesheet" href="https://cdn.knightlab.com/libs/storymapjs/latest/css/storymap.css">
    <script type="text/javascript" src="https://cdn.knightlab.com/libs/storymapjs/latest/js/storymap-min.js"></script>
    <style>
        html, body { width: 100%; height: 100%; margin: 0; padding: 0; overflow: hidden; }
        #storymap-container { width: 100%; height: 100%; }
        .error-container {
            padding: 40px;
            text-align: center;
            color: #666;
            font-family: sans-serif;
        }
        .error-container h2 { color: #c00; margin-bottom: 1rem; }
        .error-container p { font-size: 0.9em; margin-top: 1rem; }
    </style>
</head>
<body>
    <div id="storymap-container"></div>
    <script>
        (function() {
            const id = <?php echo json_encode($id); ?>;
            let storymapInstance = null;

            console.log(`StoryMap: Loading ID ${id}...`);

            // Fetch from the API endpoint
            fetch(`api.php?id=${id}`)
                .then(res => {
                    if (!res.ok) {
                        // API returns 404 with JSON body for non-existent files
                        return res.json().then(errData => {
                            throw new Error(errData.message || "StoryMap not found");
                        }).catch(() => {
                            throw new Error("StoryMap not found (ID: " + id + ")");
                        });
                    }
                    return res.json();
                })
                .then(data => {
                    // Validate that data has the expected storymap structure
                    if (!data || !data.storymap || !data.storymap.slides) {
                        throw new Error("Invalid StoryMap data structure");
                    }

                    // Validate that slides have valid location data to prevent Leaflet errors
                    const hasValidSlides = data.storymap.slides.some(slide =>
                        slide.location &&
                        typeof slide.location.lat === 'number' &&
                        typeof slide.location.lon === 'number'
                    );

                    if (!hasValidSlides) {
                        throw new Error("StoryMap has no slides with valid location data");
                    }

                    storymapInstance = new KLStoryMap.StoryMap('storymap-container', data);
                    window.onresize = function() {
                        if (storymapInstance) {
                            storymapInstance.updateDisplay();
                        }
                    };
                })
                .catch(e => {
                    console.error("StoryMap: Failed to load", e);
                    document.getElementById('storymap-container').innerHTML = `
                        <div class="error-container">
                            <h2>Unable to Load StoryMap</h2>
                            <p>${e.message}</p>
                            <p>Please check that the ID is correct and the StoryMap exists.</p>
                        </div>`;
                });
        })();
    </script>
</body>
</html>
<?php
    exit;
}

// No ID provided - show a simple landing or redirect to editor
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StoryMapTeams</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: #f5f5f5;
        }
        .container { text-align: center; padding: 2rem; }
        h1 { color: #333; margin-bottom: 1rem; }
        p { color: #666; margin-bottom: 2rem; }
        a {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 0.5rem;
            font-weight: 500;
        }
        a:hover { background: #1d4ed8; }
    </style>
</head>
<body>
    <div class="container">
        <h1>StoryMapTeams</h1>
        <p>Create and view interactive story maps.</p>
        <a href="Edit/">Open Editor</a>
    </div>
</body>
</html>
