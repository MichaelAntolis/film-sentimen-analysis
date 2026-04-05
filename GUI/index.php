<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Sentimen Film</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <!-- HEADER -->
        <div class="header">
            <h1>🎬 Analisis Sentimen Film</h1>
            <p>Menggunakan CNN + Word2Vec | Akurasi 94.76%</p>
        </div>

        <!-- STATISTICS BAR -->
        <div class="stats-bar">
            <div class="stat-item">
                <span class="stat-value" id="totalAnalysis">0</span>
                <span class="stat-label">Total Analisis</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">94.76%</span>
                <span class="stat-label">Model Accuracy</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">5</span>
                <span class="stat-label">Genre</span>
            </div>
        </div>

        <!-- INPUT TEXTAREA -->
        <textarea id="textInput" placeholder="Masukkan review film di sini...

Contoh: The horror scenes were terrifying and kept me on edge!"></textarea>

        <!-- BUTTONS -->
        <div class="button-group">
            <button class="btn-analyze" onclick="analyze()">
                🔍 Analisis Sekarang
            </button>
            <button class="btn-clear" onclick="clearAll()">
                🗑️ Hapus
            </button>
        </div>

        <!-- LOADING -->
        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Menganalisis...</p>
        </div>

        <!-- RESULT SECTION -->
        <div class="result" id="result">
            <h3>📊 Hasil Analisis</h3>

            <!-- Sentiment Card -->
            <div class="result-card">
                <span class="label">Sentimen</span>
                <span id="sentimentBadge" class="badge"></span>
            </div>

            <!-- Confidence Card -->
            <div class="result-card">
                <div class="confidence-section">
                    <span class="label">Confidence Level</span>
                    <div class="confidence-bar">
                        <div class="confidence-fill" id="confidenceBar"></div>
                    </div>
                    <span id="confidenceText" class="confidence-text"></span>
                </div>
            </div>

            <!-- Genre Card -->
            <div class="result-card">
                <span class="label">Genre Terdeteksi</span>
                <span id="genreValue" class="value"></span>
            </div>

            <!-- Genre Confidence Card -->
            <div class="result-card">
                <span class="label">Genre Confidence</span>
                <span id="genreConfidence" class="value"></span>
            </div>

            <!-- Detail Section -->
            <div class="detail-section">
                <h4>Detail Probabilitas</h4>
                <div class="detail-row">
                    <span>😊 Positive Probability</span>
                    <strong id="posProb">0%</strong>
                </div>
                <div class="detail-row">
                    <span>😞 Negative Probability</span>
                    <strong id="negProb">0%</strong>
                </div>
                <div class="detail-row">
                    <span>📝 Tokens Processed</span>
                    <strong id="tokenCount">0</strong>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            CNN Model • Word2Vec Embedding • Flask Backend
        </div>
    </div>

    <script>
        let analysisCount = 0;

        async function analyze() {
            const text = document.getElementById("textInput").value.trim();
            const loading = document.getElementById("loading");
            const result = document.getElementById("result");
            const btn = document.querySelector(".btn-analyze");

            if (!text) {
                alert("⚠️ Mohon masukkan teks review terlebih dahulu!");
                return;
            }

            // Show loading
            loading.classList.add("active");
            result.classList.remove("show");
            btn.disabled = true;

            try {
                const response = await fetch("http://127.0.0.1:5000/predict", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        text: text
                    })
                });

                const data = await response.json();

                if (data.status === "success") {
                    displayResult(data);
                    analysisCount++;
                    document.getElementById("totalAnalysis").textContent = analysisCount;
                } else {
                    alert("❌ Error: " + data.error);
                }
            } catch (error) {
                alert("❌ Gagal terhubung ke server!\n\nPastikan Flask sudah running di http://127.0.0.1:5000");
                console.error(error);
            } finally {
                loading.classList.remove("active");
                btn.disabled = false;
            }
        }

        function displayResult(data) {
            // Get elements
            const result = document.getElementById("result");
            const sentimentBadge = document.getElementById("sentimentBadge");
            const confidenceBar = document.getElementById("confidenceBar");
            const confidenceText = document.getElementById("confidenceText");
            const genreValue = document.getElementById("genreValue");
            const genreConfidence = document.getElementById("genreConfidence");
            const posProb = document.getElementById("posProb");
            const negProb = document.getElementById("negProb");
            const tokenCount = document.getElementById("tokenCount");

            // Set sentiment badge
            sentimentBadge.textContent = data.sentiment;
            sentimentBadge.className = "badge";

            if (data.sentiment === "Positive") {
                sentimentBadge.classList.add("positive");
            } else if (data.sentiment === "Negative") {
                sentimentBadge.classList.add("negative");
            } else {
                sentimentBadge.classList.add("neutral");
            }

            // Set confidence bar
            confidenceBar.style.width = data.sentiment_confidence + "%";
            confidenceText.textContent = data.sentiment_confidence + "% confidence";

            // Set genre
            genreValue.textContent = data.genre;
            genreConfidence.textContent = data.genre_confidence + "%";

            // Set probabilities
            posProb.textContent = data.positive_prob + "%";
            negProb.textContent = data.negative_prob + "%";
            tokenCount.textContent = data.token_count;

            // Show result with animation
            result.classList.add("show");
        }

        function clearAll() {
            document.getElementById("textInput").value = "";
            document.getElementById("result").classList.remove("show");
        }

        // Keyboard shortcut: Ctrl+Enter to analyze
        document.getElementById("textInput").addEventListener("keydown", function(e) {
            if (e.ctrlKey && e.key === "Enter") {
                analyze();
            }
        });
    </script>
</body>

</html>