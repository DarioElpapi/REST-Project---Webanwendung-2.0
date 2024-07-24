from flask import Flask, request, jsonify, session
from flask_cors import CORS
from flask_session import Session
import redis
import mysql.connector
from mysql.connector import Error

useremail = ""
loggedin = False

app = Flask(__name__)
# Importieren Sie die Routen
from . import create_app

# Initialisieren Sie die App
app = create_app()
app.secret_key = 'supersecretkey'
CORS(app)

# Konfigurieren von Redis für die Sitzungsverwaltung
app.config['SESSION_TYPE'] = 'redis'
app.config['SESSION_PERMANENT'] = False
app.config['SESSION_USE_SIGNER'] = True
app.config['SESSION_REDIS'] = redis.from_url('redis://localhost:6379')


def create_connection():
    return mysql.connector.connect(user='root', password='', host='localhost', database='steamdb')


@app.route('/api/login', methods=['POST'])
def login():
    global loggedin, useremail
    data = request.json
    email = data.get("email")
    passwort = data.get("passwort")

    if not email or not passwort:
        return jsonify({'error': 'Email and password are required'}), 400

    try:
        conn = create_connection()
        cursor = conn.cursor(dictionary=True)
        cursor.execute("SELECT email, passwort FROM usrs WHERE email = %s", (email,))
        user = cursor.fetchone()

        if user and passwort == user["passwort"]:
            session['email'] = user['email']
            session['loggedin'] = True
            print(session['loggedin'])
            loggedin = True
            useremail = session['email']
            return jsonify({'message': 'Login successful'}), 200
        else:
            return jsonify({'error': 'Invalid email or password'}), 401

    except Error as e:
        return jsonify({'error': str(e)}), 500

    finally:
        cursor.close()
        conn.close()

@app.route('/api/logout', methods=['POST'])
def logout():
    global loggedin
    loggedin = False
    session.pop('email', None)
    session.pop('loggedin', None)
    return jsonify({'message': 'Logged out successfully'}), 200

@app.route('/api/login-status', methods=['GET'])
def login_status():
    global loggedin, useremail
    if loggedin:
        print(useremail)
        return jsonify({'loggedin': True, 'email': useremail}), 200
    else:
        print('false')
        return jsonify({'loggedin': False}), 401

if __name__ == "__main__":
    app.run(debug=True)
