from flask import Flask, request, jsonify
from flask_cors import CORS
import pickle
import re
import nltk
import numpy as np

from nltk.tokenize import word_tokenize
from nltk.corpus import stopwords

from tensorflow.keras.models import load_model
from tensorflow.keras.preprocessing.sequence import pad_sequences

app = Flask(__name__)
CORS(app)

nltk.download("punkt")
nltk.download("stopwords")

NEGATION_WORDS = {"not", "no", "never", "n't"}
STOP_WORDS = set(stopwords.words("english")) - NEGATION_WORDS

MODEL_PATH = "model/cnn_sentiment_model.h5"
TOKENIZER_PATH = "model/tokenizer.pkl"
MAX_LEN = 200

model = load_model(MODEL_PATH)

with open(TOKENIZER_PATH, "rb") as f:
    tokenizer = pickle.load(f)

def preprocess_text(text):
    text = re.sub(r"<.*?>", " ", text)
    text = re.sub(r"[^a-zA-Z\s]", " ", text)
    text = text.lower().strip()

    tokens = word_tokenize(text)
    tokens = [t for t in tokens if t not in STOP_WORDS and len(t) > 2]

    return tokens

GENRE_KEYWORDS = {
    "Action": [
        "fight", "battle", "war", "gun", "weapon", "explosion",
        "shoot", "chase", "army", "soldier", "attack", "combat"
    ],
    "Romance": [
        "love", "romantic", "couple", "relationship", "kiss",
        "marriage", "heart", "emotion", "passion", "dating"
    ],
    "Horror": [
        "horror", "ghost", "dead", "death", "blood", "kill",
        "murder", "monster", "evil", "scary", "fear",
        "nightmare", "haunted", "frightening"
    ],
    "Comedy": [
        "funny", "laugh", "joke", "humor", "hilarious",
        "ridiculous", "parody", "satire", "comedy", "silly"
    ]
}


def detect_genre(tokens):
    """Deteksi genre berdasarkan keyword matching"""
    joined = " ".join(tokens)
    genre_scores = {}
    
    for genre, keywords in GENRE_KEYWORDS.items():
        count = sum(1 for kw in keywords if kw in joined)
        genre_scores[genre] = count
    
    if max(genre_scores.values()) == 0:
        return "Drama", 50.0  
    
    best_genre = max(genre_scores, key=genre_scores.get)
    
    total_keywords = sum(genre_scores.values())
    confidence = (genre_scores[best_genre] / total_keywords) * 100
    
    return best_genre, round(confidence, 2)

def predict_sentiment_and_genre(text):
    """Prediksi sentiment dan genre dari text"""
    
    tokens = preprocess_text(text)
    
    if not tokens:
        return {
            "sentiment": "Neutral",
            "sentiment_confidence": 50.0,
            "positive_prob": 50.0,
            "negative_prob": 50.0,
            "genre": "Unknown",
            "genre_confidence": 0.0,
            "token_count": 0
        }
    
    seq = tokenizer.texts_to_sequences([" ".join(tokens)])
    padded = pad_sequences(seq, maxlen=MAX_LEN)
    
    probs = model.predict(padded, verbose=0)[0]
    
    neg_prob = float(probs[0]) * 100  
    pos_prob = float(probs[1]) * 100  
    
    THRESHOLD = 60.0  
    
    if pos_prob >= THRESHOLD:
        sentiment = "Positive"
        sentiment_confidence = pos_prob
    elif neg_prob >= THRESHOLD:
        sentiment = "Negative"
        sentiment_confidence = neg_prob
    else:
        
        if pos_prob > neg_prob:
            sentiment = "Positive"
            sentiment_confidence = pos_prob
        else:
            sentiment = "Negative"
            sentiment_confidence = neg_prob
    
    genre, genre_confidence = detect_genre(tokens)
    
    return {
        "sentiment": sentiment,
        "sentiment_confidence": round(sentiment_confidence, 2),
        "positive_prob": round(pos_prob, 2),
        "negative_prob": round(neg_prob, 2),
        "genre": genre,
        "genre_confidence": genre_confidence,
        "token_count": len(tokens)
    }

@app.route("/")
def home():
    return "Sentiment & Genre Analysis API Running"

@app.route("/predict", methods=["POST"])
def predict():
    try:
        data = request.get_json()
        text = data.get("text", "").strip()

        if not text:
            return jsonify({"error": "Text is required"}), 400

        result = predict_sentiment_and_genre(text)

        return jsonify({
            "result": f"Analisis Sentimen {result['sentiment']} Pada Genre {result['genre']}",  # ← ORIGINAL FORMAT
            "sentiment": result["sentiment"],
            "genre": result["genre"],
            "sentiment_confidence": result["sentiment_confidence"],
            "positive_prob": result["positive_prob"],
            "negative_prob": result["negative_prob"],
            "genre_confidence": result["genre_confidence"],
            "token_count": result["token_count"],
            "status": "success"
        })
    
    except Exception as e:
        return jsonify({
            "status": "error",
            "error": str(e)
        }), 500

@app.route("/health")
def health():
    return jsonify({"status": "healthy"})

if __name__ == "__main__":
    print("API running at http://127.0.0.1:5000")
    app.run(debug=True)