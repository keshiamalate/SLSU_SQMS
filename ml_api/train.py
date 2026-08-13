import pandas as pd
import numpy as np
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split, cross_val_score
from sklearn.metrics import accuracy_score, f1_score, precision_score, recall_score
import joblib
import os
import json

# ── Synthetic data generation ──────────────────────────────────────────────────
# Mirrors realistic SLSU scholarship application patterns.
# Replace with real SLSU data when available by loading from CSV:
#   df = pd.read_csv('slsu_applications.csv')

np.random.seed(42)
n = 3000  # number of synthetic records

def generate_data(n):
    records = []
    for _ in range(n):
        gpa               = round(np.random.uniform(1.0, 3.0), 2)
        income            = np.random.choice([
                                np.random.uniform(0, 60000),
                                np.random.uniform(60000, 150000),
                                np.random.uniform(150000, 300000),
                                np.random.uniform(300000, 500000),
                            ], p=[0.35, 0.35, 0.20, 0.10])
        year_level        = np.random.randint(1, 6)
        course_match      = np.random.randint(0, 2)
        is_athlete        = np.random.choice([0, 1], p=[0.85, 0.15])
        is_student_leader = np.random.choice([0, 1], p=[0.80, 0.20])
        is_pwd            = np.random.choice([0, 1], p=[0.92, 0.08])
        is_ip             = np.random.choice([0, 1], p=[0.88, 0.12])
        is_4ps            = np.random.choice([0, 1], p=[0.70, 0.30])
        has_existing      = np.random.choice([0, 1], p=[0.75, 0.25])

        # Simulate award outcome based on realistic criteria
        score = 0
        if gpa <= 1.75:   score += 3
        elif gpa <= 2.00: score += 2
        elif gpa <= 2.50: score += 1

        if income < 60000:   score += 3
        elif income < 150000: score += 2
        elif income < 300000: score += 1

        if course_match:      score += 1
        if is_athlete:        score += 1
        if is_student_leader: score += 1
        if is_4ps:            score += 2
        if is_ip:             score += 1
        if has_existing:      score -= 2

        # Award threshold — lowered to balance classes (~45% awarded)
        threshold = np.random.uniform(4, 6)
        awarded   = 1 if score >= threshold else 0

        records.append({
            'gpa':               gpa,
            'income_normalized': min(income / 500000, 1.0),
            'year_level':        year_level,
            'course_match':      course_match,
            'is_athlete':        is_athlete,
            'is_student_leader': is_student_leader,
            'is_pwd':            is_pwd,
            'is_ip':             is_ip,
            'is_4ps':            is_4ps,
            'has_existing':      has_existing,
            'awarded':           awarded,
        })
    return pd.DataFrame(records)

print("Generating training data...")
df = generate_data(n)

# ── Features and target ────────────────────────────────────────────────────────
FEATURES = [
    'gpa', 'income_normalized', 'year_level', 'course_match',
    'is_athlete', 'is_student_leader', 'is_pwd', 'is_ip',
    'is_4ps', 'has_existing',
]

X = df[FEATURES]
y = df['awarded']

# ── Train / validation split ───────────────────────────────────────────────────
X_train, X_val, y_train, y_val = train_test_split(
    X, y, test_size=0.20, random_state=42, stratify=y
)

# ── Train Random Forest ────────────────────────────────────────────────────────
print("Training Random Forest Classifier...")
model = RandomForestClassifier(
    n_estimators=300,
    max_depth=12,
    min_samples_leaf=3,
    class_weight='balanced',
    random_state=42,
    n_jobs=-1,
)
model.fit(X_train, y_train)

# ── Evaluate ───────────────────────────────────────────────────────────────────
y_pred = model.predict(X_val)
accuracy  = accuracy_score(y_val, y_pred)
f1        = f1_score(y_val, y_pred)
precision = precision_score(y_val, y_pred)
recall    = recall_score(y_val, y_pred)

print(f"\n── Validation Results ──────────────────────")
print(f"  Accuracy  : {accuracy:.4f}")
print(f"  F1 Score  : {f1:.4f}")
print(f"  Precision : {precision:.4f}")
print(f"  Recall    : {recall:.4f}")

# Cross-validation
cv_scores = cross_val_score(model, X, y, cv=10, scoring='accuracy')
print(f"  CV Accuracy (10-fold): {cv_scores.mean():.4f} ± {cv_scores.std():.4f}")

# ── Check minimum thresholds from manuscript ───────────────────────────────────
MIN_ACCURACY = 0.85
MIN_F1       = 0.80

if accuracy < MIN_ACCURACY:
    print(f"\n⚠ WARNING: Accuracy {accuracy:.4f} is below minimum threshold {MIN_ACCURACY}")
if f1 < MIN_F1:
    print(f"\n⚠ WARNING: F1 {f1:.4f} is below minimum threshold {MIN_F1}")

# ── Save model and metadata ────────────────────────────────────────────────────
os.makedirs('model', exist_ok=True)

joblib.dump(model, 'model/rf_model.pkl')
print("\nModel saved to model/rf_model.pkl")

metadata = {
    'features':         FEATURES,
    'accuracy':         round(accuracy, 4),
    'f1_score':         round(f1, 4),
    'precision':        round(precision, 4),
    'recall':           round(recall, 4),
    'cv_accuracy':      round(float(cv_scores.mean()), 4),
    'training_records': n,
    'version':          '1.0',
}

with open('model/metadata.json', 'w') as f:
    json.dump(metadata, f, indent=2)

print("Metadata saved to model/metadata.json")
print("\n✓ Training complete. Run app.py to start the API.")
