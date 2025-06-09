import { Injectable } from '@angular/core';
import { createClient, SupabaseClient } from '@supabase/supabase-js';
import { environment } from '../../environments/environment';

@Injectable({ providedIn: 'root' })
export class SupabaseStorageService {
  private supabase: SupabaseClient;

  constructor() {
    // Initialisation unique du client Supabase
    this.supabase = createClient(
      environment.supabase.url,
      environment.supabase.key
    );
  }

  async uploadUserPhoto(file: File, userId: string): Promise<string> {
    try {
      // Authentification de l'utilisateur
      const { data: userData, error: authError } =
        await this.supabase.auth.signInWithPassword({
          email: 'agrifarm@gmail.com',
          password: 'dHAd!vcwC7Xhdb$',
        });

      if (authError) {
        console.error('Erreur lors de l’authentification :', authError.message);
        throw new Error(
          'Échec de l’authentification. Veuillez vérifier les informations de connexion.'
        );
      }

      console.log('Utilisateur authentifié :', userData);

      // Récupérer et sanitiser le nom de l'utilisateur
      const userName = userData.user?.user_metadata?.['name'] || 'utilisateur';
      const sanitizedUserName = userName.replace(/[^a-zA-Z0-9._-]/g, '_');

      // Générer un nom de fichier unique basé sur le nom de l'utilisateur
      const timestamp = Date.now();
      const sanitizedFileName = `${sanitizedUserName}_${timestamp}_${file.name.replace(
        /[^a-zA-Z0-9._-]/g,
        '_'
      )}`;
      const filePath = `${userId}/${sanitizedFileName}`;

      console.log('Tentative de téléchargement du fichier :', filePath);

      // Vérifier si le fichier existe déjà
      const { data: existingFile, error: checkError } =
        await this.supabase.storage
          .from('user-photos')
          .list(userId, { search: sanitizedFileName });

      if (checkError) {
        console.error(
          'Erreur lors de la vérification du fichier existant :',
          checkError.message
        );
        throw new Error('Erreur lors de la vérification du fichier existant.');
      }

      if (existingFile && existingFile.length > 0) {
        console.log('Un fichier avec le même nom existe déjà.');
        throw new Error('Un fichier avec le même nom existe déjà.');
      }

      const { data, error } = await this.supabase.storage
        .from('user-photos')
        .upload(filePath, file);

      if (error) {
        console.error('Erreur lors du téléchargement :', error.message);
        console.log('Détails de l’utilisateur :', userData);
        console.log('Token utilisé :', userData.session?.access_token);

        if (error.message.includes('row violates row-level security policy')) {
          console.error(
            'Vérifiez les permissions du bucket ou désactivez les politiques RLS.'
          );
        }
        throw new Error(
          'Échec du téléchargement du fichier. Veuillez réessayer.'
        );
      }

      console.log('Téléchargement réussi :', data);

      // Vérifier si le fichier a été correctement téléchargé
      if (!data || !data.path) {
        throw new Error('Le fichier n’a pas été correctement téléchargé.');
      }

      // Générer une URL signée avec une durée d'expiration d'un an (365 jours)
      const { data: signedUrlData, error: signedUrlError } =
        await this.supabase.storage
          .from('user-photos')
          .createSignedUrl(data.path, 365 * 24 * 60 * 60); // 365 jours en secondes

      if (signedUrlError) {
        console.error(
          'Erreur lors de la génération de l’URL signée :',
          signedUrlError.message
        );
        throw new Error('Échec de la génération de l’URL signée.');
      }

      console.log('URL signée générée avec succès :', signedUrlData.signedUrl);
      return signedUrlData.signedUrl;
    } catch (err) {
      console.error('Erreur inattendue :', err);
      throw err;
    }
  }
}
