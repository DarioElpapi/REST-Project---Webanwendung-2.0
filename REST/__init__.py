# __init__.py

from flask import Flask

def create_app():
    app = Flask(__name__)

    # Konfiguration der App
    app.config['MYSQL_HOST'] = 'localhost'
    app.config['MYSQL_USER'] = 'root'
    app.config['MYSQL_PASSWORD'] = ''
    app.config['MYSQL_DB'] = 'steamdb'

    # Importiere und registriere die Blueprints oder Routen
    from movies import movies_blueprint
    from genres import genres_blueprint
    from series import series_blueprint

    app.register_blueprint(movies_blueprint)
    app.register_blueprint(genres_blueprint)
    app.register_blueprint(series_blueprint)

    return app
