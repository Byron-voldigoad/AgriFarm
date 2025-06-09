import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { FormsModule } from '@angular/forms';
import { Injectable } from '@angular/core';
import { ApiService } from '../../core/services/api.service';

interface Role {
  id: number;
  nom: string;
}

interface Utilisateur {
  id: number;
  nom: string;
  email: string;
  motDePasse?: string;
  actif?: boolean;
  roles: Role[];
}

@Component({
  selector: 'app-utilisateurs',
  imports: [CommonModule, FormsModule],
  templateUrl: './utilisateurs.component.html',
  styleUrl: './utilisateurs.component.css',
})
export class UtilisateursComponent implements OnInit {
  utilisateurs: Utilisateur[] = [];
  filteredUtilisateurs: Utilisateur[] = [];
  paginatedUtilisateurs: Utilisateur[] = [];
  rolesList: Role[] = [];
  selectedRole: string = '';
  viewMode: 'table' = 'table';
  currentPage: number = 1;
  itemsPerPage: number = 10;
  showModal: boolean = false;
  isEditing: boolean = false;
  currentUtilisateur: Utilisateur | null = null;
  errorMessage: string = '';

  showRolesDropdown = false;

  constructor(private http: HttpClient, private api: ApiService) {}

  ngOnInit(): void {
    this.loadRoles();
    this.loadUtilisateurs();
  }

  loadRoles() {
    this.http.get<Role[]>('http://localhost:8000/api/roles').subscribe({
      next: (data) => (this.rolesList = data),
      error: () => (this.errorMessage = 'Erreur lors du chargement des rôles.'),
    });
  }

  loadUtilisateurs() {
    this.http
      .get<Utilisateur[]>('http://localhost:8000/api/utilisateurs')
      .subscribe({
        next: (data) => {
          this.utilisateurs = data;
          this.filterUtilisateurs();
        },
        error: () => {
          this.errorMessage = 'Erreur lors du chargement des utilisateurs.';
        },
      });
  }

  filterUtilisateurs() {
    this.currentPage = 1;
    this.filteredUtilisateurs = this.selectedRole
      ? this.utilisateurs.filter((u) =>
          u.roles.some((r) => r.nom === this.selectedRole)
        )
      : [...this.utilisateurs];
    this.updatePagination();
  }

  updatePagination() {
    const start = (this.currentPage - 1) * this.itemsPerPage;
    this.paginatedUtilisateurs = this.filteredUtilisateurs.slice(
      start,
      start + this.itemsPerPage
    );
  }

  goToPage(page: number) {
    this.currentPage = page;
    this.updatePagination();
  }

  get totalPages(): number {
    return Math.ceil(this.filteredUtilisateurs.length / this.itemsPerPage) || 1;
  }

  openAddUtilisateurDialog() {
    this.isEditing = false;
    this.currentUtilisateur = {
      id: 0,
      nom: '',
      email: '',
      motDePasse: '',
      actif: true,
      roles: [],
    };
    this.showModal = true;
  }

  openEditUtilisateurDialog(utilisateur: Utilisateur) {
    this.isEditing = true;
    this.currentUtilisateur = { ...utilisateur, roles: [...utilisateur.roles] };
    this.showModal = true;
  }

  saveUtilisateur() {
    if (!this.currentUtilisateur) return;
    const payload = {
      ...this.currentUtilisateur,
      roles: this.currentUtilisateur.roles.map((r) => r.id),
    };
    if (this.isEditing) {
      this.http
        .put(
          `http://localhost:8000/api/utilisateurs/${this.currentUtilisateur.id}`,
          payload
        )
        .subscribe({
          next: () => {
            this.loadUtilisateurs();
            this.showModal = false;
          },
          error: () => {
            this.errorMessage = 'Erreur lors de la modification.';
          },
        });
    } else {
      this.http
        .post('http://localhost:8000/api/utilisateurs', payload)
        .subscribe({
          next: () => {
            this.loadUtilisateurs();
            this.showModal = false;
          },
          error: () => {
            this.errorMessage = "Erreur lors de l'ajout.";
          },
        });
    }
  }

  deleteUtilisateur(utilisateur: Utilisateur) {
    if (!confirm('Supprimer cet utilisateur ?')) return;
    this.http
      .delete(`http://localhost:8000/api/utilisateurs/${utilisateur.id}`)
      .subscribe({
        next: () => this.loadUtilisateurs(),
        error: () => {
          this.errorMessage = 'Erreur lors de la suppression.';
        },
      });
  }

  // Désactiver/réactiver un utilisateur
  toggleActivation(utilisateur: Utilisateur) {
    const payload: any = { actif: !utilisateur.actif };
    // Si l'API exige les rôles à chaque update, envoyer les IDs
    if (utilisateur.roles && utilisateur.roles.length > 0) {
      payload.roles = utilisateur.roles.map((r) => r.id);
    }
    this.http
      .put(`http://localhost:8000/api/utilisateurs/${utilisateur.id}`, payload)
      .subscribe({
        next: () => this.loadUtilisateurs(),
        error: () => {
          this.errorMessage = "Erreur lors du changement d'état.";
        },
      });
  }

  closeModal() {
    this.showModal = false;
    this.currentUtilisateur = null;
    this.errorMessage = '';
  }

  // Getter pour éviter l'erreur NG1: Object is possibly 'null' dans le template
  get safeUtilisateur(): Utilisateur {
    return this.currentUtilisateur
      ? this.currentUtilisateur
      : { id: 0, nom: '', email: '', motDePasse: '', actif: true, roles: [] };
  }

  // Pour le multi-select Angular [(ngModel)] sur objets
  compareRoles = (r1: Role, r2: Role) => r1 && r2 && r1.id === r2.id;

  toggleRolesDropdown() {
    this.showRolesDropdown = !this.showRolesDropdown;
  }

  isRoleSelected(role: Role): boolean {
    return this.safeUtilisateur.roles.some((r) => r.id === role.id);
  }

  toggleRole(role: Role) {
    if (this.isRoleSelected(role)) {
      this.safeUtilisateur.roles = this.safeUtilisateur.roles.filter(
        (r) => r.id !== role.id
      );
    } else {
      this.safeUtilisateur.roles = [...this.safeUtilisateur.roles, role];
    }
  }

  removeRole(role: Role) {
    this.safeUtilisateur.roles = this.safeUtilisateur.roles.filter(
      (r) => r.id !== role.id
    );
  }
}
