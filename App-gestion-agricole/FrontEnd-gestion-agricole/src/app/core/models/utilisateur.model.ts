export interface Role {
  id: number;
  nom: string;
}

export interface Utilisateur {
  id: number;
  nom: string;
  email: string;
  motDePasse?: string;
  actif?: boolean;
  photo?: string;
  roles: Role[];
  created_at?: string;
  updated_at?: string;
} 