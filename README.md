# 🎬 Movie Sentiment & Genre Analysis System

Sistem Analisis Sentimen Film berbasis Deep Learning menggunakan arsitektur **CNN (Convolutional Neural Network)** dan **Word2Vec**. Proyek ini dikembangkan untuk mengklasifikasikan sentimen ulasan film (Positif/Negatif) dan mendeteksi genre secara otomatis berdasarkan konteks teks.


## 🌟 Fitur Utama
- **Analisis Sentimen Real-time**: Memprediksi apakah sebuah ulasan bersifat positif atau negatif dengan akurasi tinggi.
- **Deteksi Genre Otomatis**: Mengidentifikasi genre film (Action, Horror, Romance, Comedy, Drama) menggunakan pencocokan semantik.
- **Visualisasi Hasil**: Menampilkan bar tingkat kepercayaan (*confidence level*) dan probabilitas detail untuk setiap analisis.
- **Preprocessing Cerdas**: Menangani pembersihan teks, tokenisasi, dan manajemen kata negasi (seperti "not", "never") agar hasil prediksi tetap akurat.
- **Antarmuka Modern**: Desain GUI berbasis web yang responsif dengan estetika premium (Glassmorphism).

## 🛠️ Tech Stack
### **Frontend**
- **HTML5 & CSS3**: Struktur dan styling (Vanilla CSS).
- **JavaScript**: Menangani *Asynchronous Fetch API* ke server Flask.
- **PHP**: Sebagai wrapper aplikasi di lingkungan server lokal (XAMPP).

### **Backend (AI Engine)**
- **Python Flask**: Framework backend untuk melayani API prediksi.
- **TensorFlow/Keras**: Digunakan untuk memuat model CNN (.h5).
- **NLTK (Natural Language Toolkit)**: Digunakan untuk tokenisasi dan pembersihan teks.
- **Pickle**: Digunakan untuk memuat Tokenizer yang telah dilatih.

## 📂 Struktur Folder
```text
3_ProjectNLP_Gasal25-26/
├── GUI/
│   ├── app.py              # Backend Flask (API)
│   ├── index.php           # Antarmuka Utama
│   ├── style.css           # Styling CSS
│   └── model/
│       ├── cnn_sentiment_model.h5  # Model Deep Learning
│       └── tokenizer.pkl           # Tokenizer Data
├── Training/
│   ├── ProjectUAS.ipynb    # Notebook Pelatihan Model
│   └── dataset/            # Data ulasan untuk training
└── README.md               # Dokumentasi proyek
```

## 🚀 Panduan Instalasi & Pengoperasian

### **1. Persiapan Lingkungan**
Pastikan Anda memiliki **XAMPP** (untuk Apache) dan **Python 3.x** terinstal di komputer Anda.

### **2. Instalasi Library Python**
Buka terminal/CMD dan jalankan perintah berikut untuk menginstal dependensi backend:
```bash
pip install flask flask-cors tensorflow nltk numpy
```

### **3. Menjalankan Backend**
Masuk ke folder `GUI` dan jalankan script Python:
```bash
cd "GUI"
python app.py
```
*Pastikan terminal menampilkan:* `API running at http://127.0.0.1:5000`

### **4. Menjalankan Frontend**
1. Pindahkan atau pastikan folder proyek berada dalam direktori `htdocs` XAMPP.
2. Jalankan **Apache** melalui XAMPP Control Panel.
3. Buka browser dan akses:
   `http://localhost/Semester 7/NLP/projek/3_ProjectNLP_Gasal25-26/GUI/index.php`

## 📊 Detail Model
- **Algoritma**: CNN dengan Layer Konvolusi 1D.
- **Embedding**: Word2Vec / Tokenization.
- **Dataset**: Movie Reviews (IMDb/Scraped Data).
- **Status Akurasi Akhir**: **94.76%**.

---
