# series.py

from flask import Blueprint, request, jsonify, session
import mysql.connector
from mysql.connector import Error
from ..config import DATABASE_CONFIG

series_blueprint = Blueprint('series', __name__)

def create_connection():
    return mysql.connector.connect(user='root', database='steamdb')

@series_blueprint.route('/api/series', methods=['POST'])
def add_series():
    from ..app import useremail
    data = request.json
    title = data.get('title')
    erscheinungsjahr = data.get('erscheinungsjahr')
    genre_id = data.get('genre')
    staffeln = data.get('staffeln')
    imdb_link = data.get('imdb_link')
    bewertung = data.get('bewertung')
    user_email = useremail
    print(title)
    print(erscheinungsjahr)
    print(genre_id)
    print(staffeln)
    print(user_email)
    print(imdb_link)
    print(bewertung)

    if not all([title, erscheinungsjahr, genre_id, staffeln, imdb_link, bewertung, user_email]):
        return jsonify({'error': 'Missing data'}), 400

    try:
        conn = create_connection()
        cursor = conn.cursor()
        

        insert_series_query = """
        INSERT INTO serien (title, erscheinungsjahr, genre, staffeln, link, bewertung)
        VALUES (%s, %s, %s, %s, %s, %s)
        """
        cursor.execute(insert_series_query, (title, erscheinungsjahr, genre_id, staffeln, imdb_link, bewertung))
        last_series_id = cursor.lastrowid
        insert_favorite_query = """
        INSERT INTO user_serien (email, serie) VALUES (%s, %s)
        """
        cursor.execute(insert_favorite_query, (user_email, last_series_id))

        conn.commit()
        return jsonify({'message': 'Series added successfully', 'serie_id': last_series_id}), 201

    except Error as e:
        return jsonify({'error': str(e)}), 500

    finally:
        cursor.close()
        conn.close()


@series_blueprint.route('/api/series/show', methods=['GET'])
def get_favorite_series():
    from ..app import useremail
    user_email = useremail
    if not user_email:
        return jsonify({'error': 'User not logged in'}), 401
    
    try:
        conn = create_connection()
        cursor = conn.cursor(dictionary=True)
        
        sql = """
        SELECT serien.id, serien.title, serien.erscheinungsjahr, serien.link, serien.staffeln, genres.genre, serien.bewertung 
        FROM serien 
        INNER JOIN user_serien ON serien.id = user_serien.serie
        INNER JOIN genres ON serien.genre = genres.id
        WHERE user_serien.email = %s
        """
        cursor.execute(sql, (user_email,))
        series = cursor.fetchall()
        
        return jsonify(series), 200

    except Error as e:
        return jsonify({'error': str(e)}), 500

    finally:
        cursor.close()
        conn.close()

@series_blueprint.route('/api/series/<int:serie_id>', methods=['DELETE'])
def delete_serie(serie_id):
    from ..app import useremail
    user_email = useremail
    print(user_email)
    if not user_email:
        return jsonify({'error': 'User not logged in'}), 401

    try:
        conn = create_connection()
        cursor = conn.cursor()
        
        # Check if the serie belongs to the user
        check_query = """
        SELECT * FROM user_serien WHERE email = %s AND serie = %s
        """
        cursor.execute(check_query, (user_email, serie_id))
        result = cursor.fetchone()

        if not result:
            return jsonify({'error': 'Serie not found in user favorites'}), 404

        # Delete from user_serien first to maintain referential integrity
        delete_user_movie_query = """
        DELETE FROM user_serien WHERE email = %s AND serie = %s
        """
        cursor.execute(delete_user_movie_query, (user_email, serie_id))
        
        # Now delete from serien table if it's not a favorite of any other user
        check_other_users_query = """
        SELECT * FROM user_serien WHERE serie = %s
        """
        cursor.execute(check_other_users_query, (serie_id,))
        if cursor.fetchone() is None:
            delete_serie_query = """
            DELETE FROM serien WHERE id = %s
            """
            cursor.execute(delete_serie_query, (serie_id,))

        conn.commit()
        return jsonify({'message': 'Serie deleted successfully'}), 200

    except Error as e:
        return jsonify({'error': str(e)}), 500

    finally:
        cursor.close()
        conn.close()
