import { useEffect, type ReactNode } from 'react'
import { useQuery } from '@tanstack/react-query'
import { sessionQueryOptions } from '@/modules/auth/services/auth.service'
import { useSessionStore } from '@/shared/stores/session.store'

/**
 * Carrega a sessão atual (/auth/me) na inicialização e mantém o
 * estado global de sessão sincronizado com o cache do TanStack Query.
 */
export function SessionProvider({ children }: { children: ReactNode }) {
  const { data, isSuccess, isError } = useQuery(sessionQueryOptions)
  const setSession = useSessionStore((state) => state.setSession)
  const setGuest = useSessionStore((state) => state.setGuest)

  useEffect(() => {
    if (isSuccess && data) setSession(data)
  }, [isSuccess, data, setSession])

  useEffect(() => {
    if (isError) setGuest()
  }, [isError, setGuest])

  return children
}
