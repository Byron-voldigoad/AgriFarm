import { Component } from '@angular/core';
import { 
  LucideAngularModule,
  Leaf,
  LineChart,
  Users,
  Tractor,
  CloudRain,
  CreditCard,
  Menu,
  X,
  Settings,
  UserCog,
  HardDrive,
  Bell,
  ClipboardList,
  Map,
  BarChart2,
  AlertCircle,
  LucideIconData
} from 'lucide-angular';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';

type IconName = 'Leaf' | 'LineChart' | 'Users' | 'Tractor' | 'CloudRain' | 'CreditCard' | 'Menu' | 'X' | 'Settings' | 'UserCog' | 'HardDrive' | 'Bell' | 'ClipboardList' | 'Map' | 'BarChart2' | 'AlertCircle';

interface NavLink {
  path: string;
  icon: IconName;
  label: string;
  adminOnly?: boolean;
}

@Component({
  selector: 'app-sidebar',
  standalone: true,
  imports: [CommonModule, RouterModule, LucideAngularModule],
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.css']
})
export class SidebarComponent {
  icons = {
    Leaf, LineChart, Users, Tractor, CloudRain, 
    CreditCard, Menu, X, Settings, UserCog,
    HardDrive, Bell, ClipboardList, Map, BarChart2, AlertCircle
  };

  mobileMenuOpen = false;
  isAdmin = true; // À remplacer par votre logique d'authentification

  navLinks: NavLink[] = [
    { path: '/dashboard', icon: 'LineChart', label: 'Tableau de bord' },
    { path: '/cultures', icon: 'Leaf', label: 'Cultures' },
    { path: '/parcelles', icon: 'Map', label: 'Parcelles' },
    { path: '/taches', icon: 'ClipboardList', label: 'Tâches' },
    { path: '/rendements', icon: 'BarChart2', label: 'Rendements' },
    { path: '/capteurs', icon: 'HardDrive', label: 'Capteurs IoT' },
    // Liens admin
    { path: '/Assistance', icon: 'AlertCircle', label: 'Assistance IA', adminOnly: true },
    { path: '/utilisateurs', icon: 'UserCog', label: 'Gestion Utilisateurs', adminOnly: true },

  ];

  toggleMenu() {
    this.mobileMenuOpen = !this.mobileMenuOpen;
  }

  getFilteredLinks(): NavLink[] {
    return this.navLinks.filter(link => !link.adminOnly || this.isAdmin);
  }

  getIcon(iconName: IconName): LucideIconData {
    return this.icons[iconName];
  }
}