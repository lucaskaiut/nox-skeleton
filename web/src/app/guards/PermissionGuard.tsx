import type { ReactNode } from 'react'
import { ShieldAlert } from 'lucide-react'
import type { Permission } from '@/shared/constants/permissions'
import { usePermissions } from '@/shared/hooks/usePermissions'
import { ButtonLink, Card, EmptyState } from '@/shared/design-system'

interface PermissionGuardProps {
  permission?: Permission
  anyOf?: Permission[]
  fallback?: ReactNode
  children: ReactNode
}

/**
 * Renderiza o conteúdo apenas quando o usuário possui a permissão exigida.
 */
export function PermissionGuard({ permission, anyOf, fallback, children }: PermissionGuardProps) {
  const { can, canAny } = usePermissions()

  const allowed =
    (permission === undefined || can(permission)) && (anyOf === undefined || canAny(anyOf))

  if (!allowed) {
    if (fallback !== undefined) return fallback

    return (
      <Card>
        <EmptyState
          icon={ShieldAlert}
          title="Acesso restrito"
          description="Você não possui permissão para acessar esta área. Fale com o administrador da sua organização."
          action={
            <ButtonLink to="/dashboard" variant="secondary">
              Voltar ao dashboard
            </ButtonLink>
          }
        />
      </Card>
    )
  }

  return children
}

/**
 * Renderiza os filhos somente com permissão — sem fallback visual.
 * Útil para botões e itens de menu.
 */
export function Can({
  permission,
  anyOf,
  children,
}: {
  permission?: Permission
  anyOf?: Permission[]
  children: ReactNode
}) {
  return (
    <PermissionGuard permission={permission} anyOf={anyOf} fallback={null}>
      {children}
    </PermissionGuard>
  )
}
