import { useState } from 'react'
import { FolderTree, Pencil, Plus, Trash2 } from 'lucide-react'
import { Button, ButtonLink, ConfirmDialog, DataTable, EmptyState, Page, PageContent, PageHeader, type Column, Modal, Form, TextField, TextareaField } from '@/shared/design-system'
import { Can } from '@/app/guards/PermissionGuard'; import { Permission } from '@/shared/constants/permissions'; import { usePermissions } from '@/shared/hooks/usePermissions'
import { isApiError } from '@/shared/api/errors'; import { applyApiErrorsToForm } from '@/shared/utils/forms'
import { useForm } from 'react-hook-form'; import { zodResolver } from '@hookform/resolvers/zod'; import { z } from 'zod'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'; import { toast } from '@/shared/stores/toast.store'
import { categoriesService, type Category } from '../../posts/services/posts.service'

const catSchema = z.object({ name: z.string().min(1, 'Informe o nome'), description: z.string(), parent_id: z.number().nullable() })
type CatForm = z.infer<typeof catSchema>
const ck = { all: ['categories'] as const, list: () => ['categories', 'list'] as const }

export default function CategoriesPage() {
  const { can } = usePermissions(); const qc = useQueryClient(); const q = useQuery({ queryKey: ck.list(), queryFn: categoriesService.list })
  const [open, setOpen] = useState(false); const [editing, setEditing] = useState<Category | null>(null); const [td, setTd] = useState<Category | null>(null)
  const sm = useMutation({ mutationFn: (d: CatForm) => editing ? categoriesService.update(editing.id, { ...d, parent_id: d.parent_id }) : categoriesService.create({ ...d, parent_id: d.parent_id ?? undefined }), onSuccess: () => { qc.invalidateQueries({ queryKey: ck.all }); setOpen(false); setEditing(null); toast.success(editing ? 'Atualizada' : 'Criada') } })
  const dm = useMutation({ mutationFn: (id: number) => categoriesService.remove(id), onSuccess: () => { qc.invalidateQueries({ queryKey: ck.all }); setTd(null); toast.success('Removida') } })
  const form = useForm<CatForm>({ resolver: zodResolver(catSchema), defaultValues: { name: '', description: '', parent_id: null } })
  const openEdit = (cat: Category) => { setEditing(cat); form.reset({ name: cat.name, description: cat.description ?? '', parent_id: cat.parent_id }); setOpen(true) }
  const openCreate = () => { setEditing(null); form.reset({ name: '', description: '', parent_id: null }); setOpen(true) }
  const handleSubmit = async (v: CatForm) => { try { await sm.mutateAsync(v) } catch (e) { if (isApiError(e) && e.status === 422) applyApiErrorsToForm(form, e) } }
  const cats = q.data ?? []
  const cols: Array<Column<Category>> = [
    { key: 'name', header: 'Nome', render: (c) => <span className="font-medium text-foreground">{c.name}</span> },
    { key: 'description', header: 'Descrição', render: (c) => <span className="text-muted">{c.description || '—'}</span> },
    { key: 'children', header: 'Subcategorias', render: (c) => <span className="text-muted">{c.children?.length || 0}</span> },
    { key: 'actions', header: <span className="sr-only">Ações</span>, className: 'w-24 text-right', render: (c) => (<div className="flex items-center justify-end gap-1">{can(Permission.POST_UPDATE) && <Button variant="ghost" size="sm" onClick={() => openEdit(c)}><Pencil className="size-4" /></Button>}{can(Permission.POST_DELETE) && <Button variant="ghost" size="sm" onClick={() => setTd(c)} className="text-danger hover:bg-danger-soft hover:text-danger"><Trash2 className="size-4" /></Button>}</div>) },
  ]
  return (<Page><PageHeader title="Categorias" description="Organize o conteúdo." breadcrumb={[{ label: 'Dashboard', to: '/dashboard' }, { label: 'Categorias' }]} actions={<Can permission={Permission.POST_CREATE}><ButtonLink to="#" onClick={(e) => { e.preventDefault(); openCreate() }}><Plus className="size-4" />Nova</ButtonLink></Can>} /><PageContent>
    <DataTable caption="Categorias" columns={cols} rows={cats} rowKey={(c) => c.id} loading={q.isPending} emptyState={<EmptyState icon={FolderTree} title="Nenhuma categoria" action={<Can permission={Permission.POST_CREATE}><Button variant="primary" size="sm" onClick={openCreate}><Plus className="size-4" />Nova</Button></Can>} />} />
  </PageContent>
    <Modal open={open} onClose={() => { setOpen(false); setEditing(null) }} title={editing ? 'Editar categoria' : 'Nova categoria'} size="sm" footer={<><Button variant="secondary" onClick={() => { setOpen(false); setEditing(null) }}>Cancelar</Button><Button variant="primary" onClick={form.handleSubmit(handleSubmit)} loading={sm.isPending}>Salvar</Button></>}><Form form={form} onSubmit={handleSubmit} className="space-y-4"><TextField name="name" label="Nome" required /><TextareaField name="description" label="Descrição" rows={3} /></Form></Modal>
    <ConfirmDialog open={td !== null} onClose={() => setTd(null)} onConfirm={() => { if (td) dm.mutate(td.id) }} loading={dm.isPending} title="Excluir categoria" description={<>Excluir <strong>{td?.name}</strong>?</>} confirmLabel="Excluir" />
  </Page>)
}
