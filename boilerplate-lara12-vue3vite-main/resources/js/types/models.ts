/**
 * Model Type Definitions
 *
 * These interfaces represent the core data models in the application.
 */

export interface User {
  id: number;
  name: string;
  email: string;
  phone: string | null;
  profile_picture: string | null;
  last_active_at: string | null;
  token?: string; // API authentication token
  organizations?: Organization[];
  authorities?: Authority[];
  identifiers?: UserIdentifier[];
  created_at: string;
  updated_at: string;
}

export interface UserIdentifier {
  id: number;
  user_id: number;
  identifier_type: string;
  identifier_value: string;
  is_verified: boolean;
  verified_at: string | null;
  deleted_at: string | null;
  created_at: string;
  updated_at: string;
}

export interface Organization {
  id: number;
  name: string;
  code: string;
  description: string | null;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

export interface Authority {
  id: number;
  name: string;
  description: string | null;
  is_active: boolean;
  application?: Application;
  menus?: Menu[];
  created_at: string;
  updated_at: string;
}

export interface Application {
  id: number;
  name: string;
  url: string;
  description: string | null;
  icon: string | null;
  is_active: boolean;
  order: number;
  created_at: string;
  updated_at: string;
}

export interface Menu {
  id: number;
  name: string;
  url: string;
  icon: string | null;
  order: number;
  is_active: boolean;
  application_id: number;
  parent_id: number | null;
  application: Application;
  menu_induk?: Menu | null;
  full_path?: string;
  children?: Menu[];
  created_at: string;
  updated_at: string;
}

export interface Action {
  id: number;
  name: string;
  code: string;
  description: string | null;
  created_at: string;
  updated_at: string;
}

export interface Notification {
  id: string;
  type: string;
  notifiable_type: string;
  notifiable_id: number;
  data: {
    title: string;
    description: string;
    path: string;
    additional_data?: any;
  };
  read_at: string | null;
  created_at: string;
  updated_at: string;
}
