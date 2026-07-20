import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { http } from '@/shared/api/http'
import { toast } from '@/shared/stores/toast.store'
import { isApiError } from '@/shared/api/errors'
import { applyApiErrorsToForm } from '@/shared/utils/forms'
import type { ApiResponse } from '@/shared/types/api'
import {
  Button, Card, CardContent, Form, Page, PageContent, PageHeader,
  SelectField, TextareaField, TextField,
} from '@/shared/design-system'

interface EditorialSettings {
  company: string | null
  audience: string[] | null
  tone: string | null
  content_rules: string[] | null
  default_status: string | null
  min_content_length: number | null
}

const schema = z.object({
  company: z.string(),
  audience: z.string(),
  tone: z.string(),
  content_rules: z.string(),
  default_status: z.string(),
  min_content_length: z.string(),
})

type FormValues = z.infer<typeof schema>

function fetchSettings() {
  return http.get<ApiResponse<EditorialSettings>>('/ai/editorial-settings').then((r) => r.data.data)
}

function saveSettings(data: EditorialSettings) {
  return http.put('/ai/editorial-settings', data)
}

export default function EditorialSettingsPage() {
  const qc = useQueryClient()
  const query = useQuery({ queryKey: ['editorial-settings'], queryFn: fetchSettings })
  const mutation = useMutation({ mutationFn: saveSettings, onSuccess: () => { qc.invalidateQueries({ queryKey: ['editorial-settings'] }); toast.success('Configurações salvas') } })

  const form = useForm<FormValues>({
    resolver: zodResolver(schema),
    values: {
      company: query.data?.company ?? '',
      audience: query.data?.audience?.join(', ') ?? '',
      tone: query.data?.tone ?? 'profissional',
      content_rules: query.data?.content_rules?.join('\n') ?? '',
      default_status: query.data?.default_status ?? 'draft',
      min_content_length: String(query.data?.min_content_length ?? 200),
    },
  })

  const handleSubmit = async (values: FormValues) => {
    try {
      await mutation.mutateAsync({
        company: values.company || null,
        audience: values.audience ? values.audience.split(',').map((s) => s.trim()).filter(Boolean) : null,
        tone: values.tone || null,
        content_rules: values.content_rules ? values.content_rules.split('\n').map((s) => s.trim()).filter(Boolean) : null,
        default_status: values.default_status || null,
        min_content_length: parseInt(values.min_content_length, 10) || 200,
      })
    } catch (error) {
      if (isApiError(error) && error.status === 422) applyApiErrorsToForm(form, error)
    }
  }

  return (
    <Page>
      <PageHeader
        title="Configurações editoriais"
        description="Defina o tom, audiência e regras para a geração de conteúdo por IA."
        breadcrumb={[{ label: 'Dashboard', to: '/dashboard' }, { label: 'Configurações' }]}
      />
      <PageContent className="max-w-3xl">
        <Card>
          <CardContent>
            <Form form={form} onSubmit={handleSubmit} className="space-y-6">
              <TextField name="company" label="Nome da empresa" placeholder="Nox Soluções em Tecnologia" />
              <TextField name="audience" label="Audiência" hint="Separada por vírgulas. Ex.: empresas, empreendedores, gestores" />
              <SelectField name="tone" label="Tom de voz" options={[
                { value: 'profissional', label: 'Profissional' }, { value: 'casual', label: 'Casual' },
                { value: 'técnico', label: 'Técnico' }, { value: 'inspirador', label: 'Inspirador' },
              ]} />
              <TextareaField name="content_rules" label="Regras de conteúdo" hint="Uma regra por linha" rows={6} />
              <div className="grid gap-4 sm:grid-cols-2">
                <SelectField name="default_status" label="Status padrão" options={[
                  { value: 'draft', label: 'Rascunho' }, { value: 'review', label: 'Em revisão' },
                ]} />
                <TextField name="min_content_length" label="Tamanho mínimo do conteúdo" hint="Em caracteres (texto puro, sem HTML)" type="number" />
              </div>
              <div className="flex justify-end">
                <Button type="submit" loading={mutation.isPending}>Salvar configurações</Button>
              </div>
            </Form>
          </CardContent>
        </Card>
      </PageContent>
    </Page>
  )
}
