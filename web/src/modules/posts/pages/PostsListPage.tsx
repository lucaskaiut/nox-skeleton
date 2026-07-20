import { useState } from 'react'
import { useNavigate, useSearchParams } from 'react-router'
import { FileText, Pencil, Plus, Trash2 } from 'lucide-react'
import { Badge, Button, ButtonLink, ConfirmDialog, DataTable, EmptyState, FilterBar, Page, PageContent, PageHeader, Pagination, SearchInput, Select, type Column } from '@/shared/design-system'
import { Can } from '@/app/guards/PermissionGuard'
import { Permission } from '@/shared/constants/permissions'
import { usePermissions } from '@/shared/hooks/usePermissions'
import { useDebounce } from '@/shared/hooks/useDebounce'
import { formatDate } from '@/shared/utils/format'
import { useDeletePost, usePostsQuery } from '../hooks/usePosts'
import { POST_STATUS_LABELS, POST_STATUS_OPTIONS, type Post, type PostStatus } from '../services/posts.service'

const BADGE: Record<PostStatus, 'neutral' | 'primary' | 'warning' | 'success'> = { draft: 'neutral', review: 'warning', scheduled: 'primary', published: 'success', archived: 'neutral' }

export default function PostsListPage() {
  const [sp, ssp] = useSearchParams(); const [search, setSearch] = useState(sp.get('search') ?? ''); const ds = useDebounce(search)
  const page = Number(sp.get('page') ?? 1); const sf = sp.get('status') ?? ''
  const nav = useNavigate(); const { can } = usePermissions(); const [del, setDel] = useState<Post | null>(null); const dm = useDeletePost()
  const q = usePostsQuery({ page, per_page: 10, search: ds || undefined, status: sf || undefined })
  const up = (n: Record<string, string>) => ssp((p) => { for (const [k, v] of Object.entries(n)) v ? p.set(k, v) : p.delete(k); if (n.search !== sp.get('search')) p.delete('page'); if (n.page === '1') p.delete('page'); return p }, { replace: true })
  const canMut = can(Permission.POST_UPDATE) || can(Permission.POST_DELETE)
  const cols: Array<Column<Post>> = [
    { key: 'title', header: 'Título', render: (p) => <div className="min-w-0"><p className="truncate font-medium text-foreground">{p.title}</p><p className="truncate text-[13px] text-muted">/{p.slug}</p></div> },
    { key: 'status', header: 'Status', render: (p) => <Badge variant={BADGE[p.status]}>{POST_STATUS_LABELS[p.status]}</Badge> },
    { key: 'author', header: 'Autor', render: (p) => <span className="text-muted">{p.author?.name ?? '—'}</span> },
    { key: 'published_at', header: 'Publicado em', render: (p) => <span className="text-muted">{p.published_at ? formatDate(p.published_at) : '—'}</span> },
    ...(canMut ? [{ key: 'actions', header: <span className="sr-only">Ações</span>, className: 'w-24 text-right', render: (p: Post) => (<div className="flex items-center justify-end gap-1">{can(Permission.POST_UPDATE) && <Button variant="ghost" size="sm" onClick={() => nav(`/posts/${p.id}/edit`)} aria-label={`Editar ${p.title}`}><Pencil className="size-4" /></Button>}{can(Permission.POST_DELETE) && <Button variant="ghost" size="sm" onClick={() => setDel(p)} aria-label={`Excluir ${p.title}`} className="text-danger hover:bg-danger-soft hover:text-danger"><Trash2 className="size-4" /></Button>}</div>) } satisfies Column<Post>] : []),
  ]
  return (<Page><PageHeader title="Posts" description="Gerencie o conteúdo do blog." breadcrumb={[{ label: 'Dashboard', to: '/dashboard' }, { label: 'Posts' }]} actions={<Can permission={Permission.POST_CREATE}><ButtonLink to="/posts/create"><Plus className="size-4" />Novo post</ButtonLink></Can>} /><PageContent>
    <FilterBar><SearchInput placeholder="Buscar por título..." value={search} onChange={(e) => { setSearch(e.target.value); up({ search: e.target.value, page: '' }) }} /><Select options={[{ value: '', label: 'Todos' }, ...POST_STATUS_OPTIONS]} value={sf} onChange={(e) => up({ status: e.target.value, page: '' })} className="w-48" /></FilterBar>
    <DataTable caption="Posts" columns={cols} rows={q.data?.data ?? []} rowKey={(p) => p.id} loading={q.isPending} emptyState={<EmptyState icon={FileText} title={ds || sf ? 'Nenhum resultado' : 'Nenhum post'} description={ds || sf ? 'Tente ajustar os filtros.' : 'Crie o primeiro post.'} action={!ds && !sf ? <Can permission={Permission.POST_CREATE}><ButtonLink to="/posts/create"><Plus className="size-4" />Novo post</ButtonLink></Can> : undefined} />} />
    {q.data && <Pagination meta={q.data.meta} onPageChange={(n) => up({ page: String(n) })} />}
  </PageContent><ConfirmDialog open={del !== null} onClose={() => setDel(null)} onConfirm={() => { if (del) dm.mutate(del.id, { onSettled: () => setDel(null) }) }} loading={dm.isPending} title="Excluir post" description={<>Excluir <strong>{del?.title}</strong>?</>} confirmLabel="Excluir" /></Page>)
}
