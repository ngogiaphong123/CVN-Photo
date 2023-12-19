import {
  ContextMenu,
  ContextMenuContent,
  ContextMenuItem,
  ContextMenuTrigger,
} from '@/components/ui/context-menu'
import { SidebarItem } from '@components/layouts/sidebar'
import { cn } from '@/lib/utils'
import { Link, useLocation } from 'react-router-dom'
import { buttonVariants } from '@components/ui/button'

export default function CategorySidebar({
  item,
  setOpen,
  setCategoryId,
}: {
  item: SidebarItem
  setOpen: React.Dispatch<React.SetStateAction<boolean>>
  setCategoryId: React.Dispatch<React.SetStateAction<string>>
}) {
  const location = useLocation()
  const { pathname } = location
  const id = item.href.split('/')[2]
  return (
    <>
      {' '}
      <ContextMenu>
        <ContextMenuTrigger>
          {' '}
          <Link
            key={item.href}
            to={item.href}
            className={cn(
              buttonVariants({ variant: 'ghost' }),
              pathname === item.href
                ? 'text-primary hover:bg-muted hover:text-primary font-bold'
                : 'hover:bg-muted hover:text-primary',
              'justify-start line-clamp-1 text-left h-12 w-[95%]',
            )}
          >
            <div className="flex items-center justify-between gap-4">
              {' '}
              <img
                className="w-8 h-8 mr-2 rounded-full"
                src={item.url}
                alt=""
              />
              <p className="hidden truncate lg:inline-block max-w-[120px] xl:max-w-[200px]">
                {item.title}
              </p>
            </div>
          </Link>
        </ContextMenuTrigger>
        <ContextMenuContent>
          <ContextMenuItem
            className="focus:text-destructive"
            onClick={() => {
              setCategoryId(id)
              setOpen(true)
            }}
          >
            Delete category
          </ContextMenuItem>
        </ContextMenuContent>
      </ContextMenu>
    </>
  )
}
