import { Photo } from '@redux/types/response.type'
import { Icon } from '@iconify/react'
import * as z from 'zod'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
} from '@/components/ui/form'

import { Input } from '@components/ui/input'
import ChangDateDialog from './dialog/change-date-dialog'
import { toastMessage } from '@lib/utils'
import { useUpdatePhoto } from '@/hooks/photo/useUpdatePhoto'

function humanFileSize(bytes: number, si = true, dp = 1): string {
  const thresh = si ? 1000 : 1024

  if (Math.abs(bytes) < thresh) {
    return bytes + ' B'
  }

  const units = si
    ? ['kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']
    : ['KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB']
  let u = -1
  const r = 10 ** dp

  do {
    bytes /= thresh
    ++u
  } while (
    Math.round(Math.abs(bytes) * r) / r >= thresh &&
    u < units.length - 1
  )

  return bytes.toFixed(dp) + ' ' + units[u]
}
const formSchema = z.object({
  description: z.string().optional(),
})

export default function PhotoInfo({ photo }: { photo: Photo }) {
  const { mutateAsync: updatePhoto } = useUpdatePhoto(photo.id)
  const form = useForm<z.infer<typeof formSchema>>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      description: photo.description,
    },
  })
  const onSubmit = async (values: z.infer<typeof formSchema>) => {
    if (!values.description) values.description = ''
    toastMessage('Updating photo...', 'default')
    await updatePhoto({
      description: values.description,
    })
    toastMessage('Photo updated!', 'default')
  }
  return (
    <div className="flex-col hidden px-4 py-8 lg:flex lg:w-3/12">
      <div className="px-4 text-2xl font-bold">Photo Information</div>
      <Form {...form}>
        <form onSubmit={form.handleSubmit(onSubmit)}>
          <div className="flex items-center justify-start w-full gap-4 p-4">
            <div className="flex flex-col w-full">
              <FormField
                control={form.control}
                name="description"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Description</FormLabel>
                    <FormControl>
                      <Input
                        {...field}
                        placeholder={
                          photo.description === ''
                            ? 'Add a description'
                            : photo.description
                        }
                        className="w-full border-b"
                      />
                    </FormControl>
                  </FormItem>
                )}
              />
            </div>
          </div>
        </form>
      </Form>
      <div className="flex items-center justify-start gap-4 p-4">
        <div>
          <Icon
            icon="material-symbols-light:photo-album-outline-rounded"
            width={24}
            height={24}
          />
        </div>
        <div className="truncate">{photo.name}</div>
      </div>
      <ChangDateDialog photo={photo} />
      <div className="flex items-center justify-start gap-4 p-4">
        <div>
          <Icon
            icon="material-symbols-light:cloud-done-outline-rounded"
            width={24}
            height={24}
          />
        </div>
        <div>Size: {humanFileSize(parseFloat(photo.size))}</div>
      </div>
    </div>
  )
}
