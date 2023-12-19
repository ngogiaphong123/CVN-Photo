import { useMemo } from 'react'
import { useLocation } from 'react-router-dom'

export default function Breadcrumbs() {
  const location = useLocation()
  const pathnames = location.pathname.split('/').filter(x => x)

  const breadcrumbs = useMemo(() => {
    return pathnames.map((path, index) => {
      const url = `/${pathnames.slice(0, index + 1).join('/')}`
      path = path.charAt(0).toUpperCase() + path.slice(1)
      return { name: path, url }
    })
  }, [pathnames])

  return (
    <div className="px-4 pt-20">
      You are here:{' '}
      {breadcrumbs.map((breadcrumb, index) => (
        <span key={breadcrumb.url}>
          <a
            href={breadcrumb.url}
            className={
              index === breadcrumbs.length - 1
                ? 'text-primary hover:text-accent'
                : 'text-gray-500 hover:text-gray-600'
            }
          >
            {breadcrumb.name}
          </a>
          {index < breadcrumbs.length - 1 && ' / '}
        </span>
      ))}
    </div>
  )
}
