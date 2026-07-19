import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { Link } from 'react-router'
import { Button, Card, CardContent, Form, Section, TextField } from '@/shared/design-system'
import { isApiError } from '@/shared/api/errors'
import { applyApiErrorsToForm } from '@/shared/utils/forms'
import { onlyDigits } from '@/shared/utils/document'
import { registerSchema, type RegisterFormValues } from '../schemas/auth.schema'
import { useRegister } from '../hooks/useAuth'

export default function RegisterPage() {
  const register = useRegister()

  const form = useForm<RegisterFormValues>({
    resolver: zodResolver(registerSchema),
    defaultValues: {
      tenant: { name: '', document: '', email: '', phone: '', domain: '' },
      user: {
        name: '',
        email: '',
        phone: '',
        document: '',
        password: '',
        password_confirmation: '',
      },
    },
  })

  const onSubmit = async (values: RegisterFormValues) => {
    try {
      await register.mutateAsync({
        tenant: {
          ...values.tenant,
          document: onlyDigits(values.tenant.document),
          domain: values.tenant.domain.toLowerCase().trim(),
        },
        user: {
          name: values.user.name,
          email: values.user.email,
          phone: values.user.phone,
          document: onlyDigits(values.user.document),
          password: values.user.password,
        },
      })
    } catch (error) {
      if (isApiError(error) && error.status === 422) {
        applyApiErrorsToForm(form, error)
      }
    }
  }

  return (
    <Card className="w-full max-w-2xl">
      <CardContent className="p-6 sm:p-8">
        <div className="mb-6">
          <h1 className="text-lg font-semibold text-foreground">Criar conta</h1>
          <p className="mt-1 text-sm text-muted">
            Configure sua organização e o usuário administrador.
          </p>
        </div>

        <Form form={form} onSubmit={onSubmit} className="space-y-8">
          <Section title="Dados da organização">
            <div className="grid gap-4 sm:grid-cols-2">
              <TextField name="tenant.name" label="Nome da empresa" required className="sm:col-span-2" />
              <TextField name="tenant.document" label="CPF ou CNPJ" placeholder="Somente números" required />
              <TextField name="tenant.phone" label="Telefone" placeholder="(41) 99999-9999" required />
              <TextField name="tenant.email" label="E-mail da empresa" type="email" required />
              <TextField
                name="tenant.domain"
                label="Domínio"
                placeholder="empresa.com.br"
                hint="Domínio usado pelos sistemas que consomem a API"
                required
              />
            </div>
          </Section>

          <Section title="Usuário administrador">
            <div className="grid gap-4 sm:grid-cols-2">
              <TextField name="user.name" label="Nome completo" required className="sm:col-span-2" />
              <TextField name="user.email" label="E-mail" type="email" autoComplete="email" required />
              <TextField name="user.phone" label="Telefone" required />
              <TextField name="user.document" label="CPF" placeholder="Somente números" required />
              <span className="hidden sm:block" />
              <TextField
                name="user.password"
                label="Senha"
                type="password"
                autoComplete="new-password"
                hint="Mínimo de 8 caracteres"
                required
              />
              <TextField
                name="user.password_confirmation"
                label="Confirmar senha"
                type="password"
                autoComplete="new-password"
                required
              />
            </div>
          </Section>

          <Button type="submit" className="w-full" loading={register.isPending}>
            Criar conta
          </Button>
        </Form>

        <p className="mt-6 text-center text-sm text-muted">
          Já possui uma conta?{' '}
          <Link to="/auth/login" className="font-medium text-primary hover:text-primary-hover">
            Entrar
          </Link>
        </p>
      </CardContent>
    </Card>
  )
}
