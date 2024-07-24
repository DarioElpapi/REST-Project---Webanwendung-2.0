# genres.py

from flask import Blueprint, jsonify
import mysql.connector
from mysql.connector import Error

genres_blueprint = Blueprint('genres', __name__)

def create_connection():
    return mysql.connector.connect(user='root', database='steamdb')

@genres_blueprint.route('/api/genres', methods=['GET'])
def get_genres():
    try:
        conn = create_connection()
        cursor = conn.cursor(dictionary=True)
        cursor.execute("SELECT * FROM genres")
        genres = cursor.fetchall()
        return jsonify(genres), 200

    except Error as e:
        return jsonify({'error': str(e)}), 500

    finally:
        cursor.close()
        conn.close()

@genres_blueprint.route('/api/genres/show/<int:genre_id>', methods=['GET'])
def get_movies_genres(genre_id):
    try:
        conn = create_connection()
        cursor = conn.cursor(dictionary=True)
        sql1 = """
               SELECT movies.title, movies.erscheinungsjahr, movies.link, movies.dauer, movies.bewertung
               FROM movies 
               INNER JOIN genres ON movies.genre = genres.id
               WHERE genres.id = %s
               """
        cursor.execute(sql1, (genre_id,))
        movies = cursor.fetchall()
        print(movies)
        
        return jsonify(movies), 200

    except Error as e:
        return jsonify({'error': str(e)}), 500

    finally:
        cursor.close()
        conn.close()

@genres_blueprint.route('/api/genres/shows/<int:genre_id>', methods=['GET'])
def get_serien_genres(genre_id):
    try:
        conn = create_connection()
        cursor = conn.cursor(dictionary=True)
        sql2 = """
               SELECT serien.title, serien.erscheinungsjahr, serien.link, serien.staffeln, serien.bewertung
               FROM serien 
               INNER JOIN genres ON serien.genre = genres.id
               WHERE genres.id = %s
               """
        cursor.execute(sql2, (genre_id,))
        serien = cursor.fetchall()
        print(serien)
        return jsonify(serien), 200

    except Error as e:
        return jsonify({'error': str(e)}), 500

    finally:
        cursor.close()
        conn.close()

