import React from 'react'
import { Dialog, DialogContent, DialogTrigger } from '@components/ui/dialog'
import { renderPhotoDetailIcon } from '@lib/utils'
import { useDeleteCategory } from '@/hooks/category/useDeleteCategory'
import { useNavigate } from 'react-router-dom'

export default function DeleteCategoryDialog({
  open,
  setOpen,
  categoryId,
  pathname,
}: {
  open: boolean
  setOpen: React.Dispatch<React.SetStateAction<boolean>>
  categoryId: string
  pathname: string
}) {
  const navigate = useNavigate()
  const { mutateAsync: deleteCategory } = useDeleteCategory()

  return (
    <Dialog onOpenChange={setOpen} open={open}>
      <DialogTrigger>
        <div className="hover:cursor-pointer">
          {renderPhotoDetailIcon('delete-outline-rounded')}
        </div>
      </DialogTrigger>
      <DialogContent>
        <div className="flex flex-col items-center justify-center w-full gap-4 p-4">
          <div className="font-bold text-l md:text-l">
            Are you sure you want to delete this category?
          </div>
          <div className="flex items-center justify-center gap-4">
            <button
              className="px-4 py-2 text-white rounded-lg bg-destructive hover:opacity-80"
              onClick={() => {
                deleteCategory(categoryId)
                if (pathname === `/category/${categoryId}`)
                  navigate('/category')
                setOpen(false)
              }}
            >
              Delete
            </button>
            <button
              className="px-4 py-2 text-white rounded-lg bg-primary hover:opacity-80"
              onClick={() => {
                setOpen(false)
              }}
            >
              Cancel
            </button>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  )
}
