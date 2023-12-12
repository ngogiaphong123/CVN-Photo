import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTrigger,
} from '@components/ui/dialog'
import { Button, buttonVariants } from '@components/ui/button'
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { zodResolver } from '@hookform/resolvers/zod'
import { useForm } from 'react-hook-form'
import * as z from 'zod'
import { cn } from '@/lib/utils'
import ChoosePhoto from '../choose-photo'
import { useState } from 'react'
import { useCreateCategory } from '../../hooks/category.hook'
import { toast } from '../ui/use-toast'

const formSchema = z.object({
  name: z.string().min(3, {
    message: 'Name must be at least 3 characters long',
  }),
  memo: z.string().optional(),
})

export default function CreateCategoryDialog() {
  const [open, setOpen] = useState(false)
  const [url, setUrl] = useState('')
  const [publicId, setPublicId] = useState('')
  const form = useForm<z.infer<typeof formSchema>>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      name: '',
      memo: '',
    },
  })
  const { mutateAsync: createCategory, isPending } = useCreateCategory()
  const onSubmit = async (values: z.infer<typeof formSchema>) => {
    if (url === '' || publicId === '') return
    if (values.memo === undefined) values.memo = ''
    await createCategory({
      name: values.name,
      memo: values.memo,
      url: url,
      publicId: publicId,
    })
    toast({
      description: `Category ${values.name} created!`,
    })
    setOpen(false)
  }
  const renderButton = () => {
    if (isPending)
      return (
        <Button
          className={'bg-muted cursor-loading hover:bg-muted'}
          type="submit"
        >
          Creating...
        </Button>
      )
    if (url === '' || publicId === '')
      return (
        <Button
          className={'bg-muted cursor-not-allowed text-primary hover:bg-muted'}
          type="submit"
        >
          Please choose a photo for your category
        </Button>
      )
    return (
      <Button
        className={'bg:primary hover:bg-opacity-60 text-white'}
        type="submit"
      >
        Create
      </Button>
    )
  }

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger
        className={cn(
          buttonVariants({ variant: 'ghost', size: 'lg' }),
          'text-accent',
        )}
      >
        <div>Create category</div>
      </DialogTrigger>
      <DialogContent>
        <DialogHeader title="Create category">
          <div className="text-lg text-primary">
            Create a new category for your photos
          </div>
        </DialogHeader>
        <Form {...form}>
          <form
            onSubmit={form.handleSubmit(onSubmit)}
            className="flex flex-col w-full p-4 space-y-4"
          >
            <FormField
              control={form.control}
              name="name"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Name</FormLabel>
                  <FormControl>
                    <Input placeholder="Name" {...field} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />
            <FormField
              control={form.control}
              name="memo"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Memo</FormLabel>
                  <FormControl>
                    <Input placeholder="Memo" {...field} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />
            <FormLabel>Category Thumbnail</FormLabel>
            <ChoosePhoto
              setUrl={setUrl}
              setPublicId={setPublicId}
              url={url}
              publicId={publicId}
            />
            {renderButton()}
          </form>
        </Form>
      </DialogContent>
    </Dialog>
  )
}
