import { Photo } from '@redux/types/response.type'
import { Icon } from '@iconify/react'
import * as z from 'zod'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import { Calendar as CalendarIcon } from 'lucide-react'
import { format } from 'date-fns'

import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { Button } from '@/components/ui/button'
import { Calendar } from '@/components/ui/calendar' 
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { cn } from '@lib/utils'
import { useState } from 'react'
import { useUpdatePhoto } from '@/hooks/photo/useUpdatePhoto'
const schema = z.object({
  takenAt: z.date({
    required_error: 'Taken date is required',
  }),
})

export default function ChangDateDialog({ photo }: { photo: Photo }) {
  const [isOpen, setIsOpen] = useState(false)
  const { mutateAsync: updatePhoto } = useUpdatePhoto(photo.id)
  const form = useForm<z.infer<typeof schema>>({
    resolver: zodResolver(schema),
  })
  function onSubmit(data: z.infer<typeof schema>) {
    const date = new Date(data.takenAt)
    const tzOffset = new Date().getTimezoneOffset() * 60000
    const localISOTime = new Date(date.getTime() - tzOffset)
      .toISOString()
      .slice(0, 19)
      .replace('T', ' ')
    updatePhoto({
      takenAt: localISOTime,
    })
    setIsOpen(false)
  }

  return (
    <Dialog open={isOpen} onOpenChange={setIsOpen}>
      <DialogTrigger>
        <div className="flex items-center justify-between p-4 rounded-lg cursor-pointer hover:bg-gray-100">
          <div className="flex gap-4">
            <div>
              <Icon
                icon="material-symbols-light:calendar-month-outline-rounded"
                width={24}
                height={24}
              />
            </div>
            <div>Taken at: {new Date(photo.takenAt).toLocaleDateString()}</div>
          </div>
          <div>
            <Icon
              icon="material-symbols-light:edit-square-outline-rounded"
              width={24}
              height={24}
            />
          </div>
        </div>
      </DialogTrigger>
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Change picture taken date</DialogTitle>
          <DialogDescription>
            Changing the date will affect the order of the photos in the album.
          </DialogDescription>
        </DialogHeader>
        <Form {...form}>
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
            <FormField
              control={form.control}
              name="takenAt"
              render={({ field }) => (
                <FormItem className="flex flex-col">
                  <FormLabel>Taken date:</FormLabel>
                  <Popover>
                    <PopoverTrigger asChild>
                      <FormControl>
                        <Button
                          variant={'outline'}
                          className={cn(
                            'w-[240px] pl-3 text-left font-normal hover:bg-primary',
                            !field.value && 'text-muted-foreground',
                          )}
                        >
                          {field.value ? (
                            format(field.value, 'PPP')
                          ) : (
                            <span>Pick a date</span>
                          )}
                          <CalendarIcon className="w-4 h-4 ml-auto opacity-50" />
                        </Button>
                      </FormControl>
                    </PopoverTrigger>
                    <PopoverContent className="w-auto p-0" align="start">
                      <Calendar
                        mode="single"
                        selected={field.value}
                        onSelect={field.onChange}
                        disabled={date =>
                          date > new Date() || date < new Date('1900-01-01')
                        }
                        initialFocus
                      />
                    </PopoverContent>
                  </Popover>
                  <FormMessage />
                </FormItem>
              )}
            />
            <Button type="submit" className="m-0 text-white">
              Submit
            </Button>
          </form>
        </Form>
      </DialogContent>
    </Dialog>
  )
}
