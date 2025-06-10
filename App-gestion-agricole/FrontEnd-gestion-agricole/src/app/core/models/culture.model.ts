export interface Culture {
  id: number;
  nom: string;
  description?: string;
  photo?: string;
  type_culture?: string;
  conditions_climatiques?: string;
  cout_estime?: number;
}
