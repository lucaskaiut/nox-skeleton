import axios from 'axios'

let csrfReady = false

/**
 * Garante o cookie XSRF-TOKEN antes de requisições de autenticação
 * (fluxo SPA do Laravel Sanctum).
 */
export async function ensureCsrfCookie(): Promise<void> {
  if (csrfReady) return

  await axios.get('/sanctum/csrf-cookie', { withCredentials: true })

  csrfReady = true
}
