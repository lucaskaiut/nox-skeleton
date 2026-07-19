import { create } from 'zustand'
import type { Session, Tenant, User, Role } from '@/shared/types/models'
import type { Permission } from '@/shared/constants/permissions'

export type SessionStatus = 'loading' | 'authenticated' | 'guest'

interface SessionState {
  status: SessionStatus
  user: User | null
  tenant: Tenant | null
  roles: Role[]
  permissions: Permission[]
  setSession: (session: Session) => void
  setGuest: () => void
}

export const useSessionStore = create<SessionState>()((set) => ({
  status: 'loading',
  user: null,
  tenant: null,
  roles: [],
  permissions: [],

  setSession: (session) =>
    set({
      status: 'authenticated',
      user: session.user,
      tenant: session.tenant,
      roles: session.roles,
      permissions: session.permissions,
    }),

  setGuest: () =>
    set({
      status: 'guest',
      user: null,
      tenant: null,
      roles: [],
      permissions: [],
    }),
}))
