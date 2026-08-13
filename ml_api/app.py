from flask import Flask, request, jsonify
import joblib
import json
import os

app = Flask(__name__)

# ── Load model and metadata on startup ────────────────────────────────────────
MODEL_PATH    = os.path.join(os.path.dirname(__file__), 'model', 'rf_model.pkl')
METADATA_PATH = os.path.join(os.path.dirname(__file__), 'model', 'metadata.json')

try:
    model = joblib.load(MODEL_PATH)
    with open(METADATA_PATH, 'r') as f:
        metadata = json.load(f)
    print("✓ Model loaded successfully.")
    print(f"  Accuracy: {metadata['accuracy']}  |  F1: {metadata['f1_score']}")
except Exception as e:
    model    = None
    metadata = {}
    print(f"✗ Failed to load model: {e}")

# ── Health check ───────────────────────────────────────────────────────────────
@app.route('/health', methods=['GET'])
def health():
    return jsonify({
        'status':   'ok' if model else 'model_not_loaded',
        'version':  metadata.get('version', 'unknown'),
        'accuracy': metadata.get('accuracy'),
        'f1_score': metadata.get('f1_score'),
    })

# ── Single prediction ──────────────────────────────────────────────────────────
@app.route('/predict', methods=['POST'])
def predict():
    if model is None:
        return jsonify({'error': 'Model not loaded'}), 503

    data = request.get_json()
    if not data:
        return jsonify({'error': 'No JSON body provided'}), 400

    try:
        features = [
            float(data.get('gpa', 2.0)),
            float(data.get('income_normalized', 0.5)),
            int(data.get('year_level', 1)),
            int(data.get('course_match', 0)),
            int(data.get('is_athlete', 0)),
            int(data.get('is_student_leader', 0)),
            int(data.get('is_pwd', 0)),
            int(data.get('is_ip', 0)),
            int(data.get('is_4ps', 0)),
            int(data.get('has_existing', 0)),
        ]

        probability = model.predict_proba([features])[0][1]
        prediction  = int(model.predict([features])[0])

        return jsonify({
            'probability': round(float(probability), 4),
            'prediction':  prediction,
            'label':       'likely_awarded' if probability >= 0.5 else 'unlikely_awarded',
        })

    except Exception as e:
        return jsonify({'error': str(e)}), 400

# ── Batch prediction ───────────────────────────────────────────────────────────
@app.route('/predict/batch', methods=['POST'])
def predict_batch():
    if model is None:
        return jsonify({'error': 'Model not loaded'}), 503

    data = request.get_json()
    if not data or 'records' not in data:
        return jsonify({'error': 'Expected JSON with a "records" array'}), 400

    results = []
    for record in data['records']:
        try:
            features = [
                float(record.get('gpa', 2.0)),
                float(record.get('income_normalized', 0.5)),
                int(record.get('year_level', 1)),
                int(record.get('course_match', 0)),
                int(record.get('is_athlete', 0)),
                int(record.get('is_student_leader', 0)),
                int(record.get('is_pwd', 0)),
                int(record.get('is_ip', 0)),
                int(record.get('is_4ps', 0)),
                int(record.get('has_existing', 0)),
            ]

            probability = model.predict_proba([features])[0][1]
            prediction  = int(model.predict([features])[0])

            results.append({
                'scholarship_id': record.get('scholarship_id'),
                'probability':    round(float(probability), 4),
                'prediction':     prediction,
            })

        except Exception as e:
            results.append({
                'scholarship_id': record.get('scholarship_id'),
                'error':          str(e),
            })

    return jsonify({'results': results})

# ── Model info ─────────────────────────────────────────────────────────────────
@app.route('/model/info', methods=['GET'])
def model_info():
    return jsonify(metadata)

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=False)
