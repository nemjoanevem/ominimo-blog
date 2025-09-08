export type PostSnapshot = {
  id: number
  user_id: number
  title: string
  body: string
  user?: { id: number; name: string; email: string }
}

/** Build safe HTML for the detail view out of a snapshot. */
export function buildPostHtml(p: PostSnapshot): string {
  const esc = (s: string) => {
    const div = document.createElement('div')
    div.innerText = s ?? ''
    return div.innerHTML
  }
  return `
    <article>
      <h1 class="text-2xl font-semibold">${esc(p.title)}</h1>
      <p class="text-sm opacity-70">${esc(p.user?.name ?? '')}</p>
      <div class="mt-4 whitespace-pre-wrap">${esc(p.body)}</div>
    </article>
  `
}

export function savePostCache(p: PostSnapshot) {
  sessionStorage.setItem(`post:snapshot:${p.id}`, JSON.stringify(p))
  sessionStorage.setItem(`post:html:${p.id}`, buildPostHtml(p))
}

export function loadPostSnapshot(id: number): PostSnapshot | null {
  const raw = sessionStorage.getItem(`post:snapshot:${id}`)
  return raw ? JSON.parse(raw) as PostSnapshot : null
}

export function loadPostHtml(id: number): string | '' {
  return sessionStorage.getItem(`post:html:${id}`) ?? ''
}
