export interface Parcelle {
    id: number;
    nom: string;
    surface: number;
    localisation: string;
    etat: 'En culture' | 'En jachère';
    agriculteur_id: number;
    culture_id?: number | null;
    stadeCroissance?: string | null;
    created_at?: string;
    updated_at?: string;
  }
  
  export interface ParcelleWithRelations extends Parcelle {
    agriculteurNom?: string;
    cultureNom?: string;
    culture?: {
      id: number;
      nom: string;
    };
  }