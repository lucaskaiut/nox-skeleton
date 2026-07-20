import { useNavigate } from 'react-router'
import { Page, PageContent, PageHeader } from '@/shared/design-system'
import { PostForm } from '../forms/PostForm'
import { useCreatePost } from '../hooks/usePosts'

export default function PostCreatePage() {
  const nav = useNavigate(); const cm = useCreatePost()

  return (<Page><PageHeader title="Novo post" breadcrumb={[{ label: 'Dashboard', to: '/dashboard' }, { label: 'Posts', to: '/posts' }, { label: 'Novo post' }]} /><PageContent><PostForm mode="create" submitting={cm.isPending} onSubmit={async (p) => { await cm.mutateAsync(p); nav('/posts') }} /></PageContent></Page>)
}
