import os
import yaml

# Chemin vers votre fichier YAML original
nelmio = 'config/packages/nelmio_api_doc.yaml'

# Chemin de destination pour le nouveau fichier YAML
destination_path = 'openapi-redoc.yaml'

# Charger et traiter le fichier YAML
try:
    with open(nelmio, 'r') as file:
        data = yaml.full_load(file)

    if 'nelmio_api_doc' in data and 'documentation' in data['nelmio_api_doc']:
        # Écrire la documentation dans le nouveau fichier, en conservant l'ordre
        with open(destination_path, 'w') as new_file:
            yaml.dump(data['nelmio_api_doc']['documentation'], new_file, sort_keys=False)
    else:
        print("Nelmio API Doc configuration file does not contain necessary keys.")
except FileNotFoundError:
    print(f"File {nelmio} not found.")
